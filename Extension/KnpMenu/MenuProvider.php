<?php
namespace BDev\Bundle\RoutingExtraBundle\Extension\KnpMenu;

use Knp\Menu\Provider\MenuProviderInterface;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;

class MenuProvider implements WarmableInterface, MenuProviderInterface
{
    protected $container;

    protected $resource;

    protected $options;

    protected $collection;

    protected $generator;

    public function __construct(ContainerInterface $container, $resource, array $options)
    {
        $this->container = $container;
        $this->resource = $resource;
        $this->setOptions($options);
    }

    /**
     * {@inheritDoc}
     */
    public function get($name, array $options = array())
    {
        $factory = $this->getFactory();
        $menu = $factory->createFromArray(
            $this->getGenerator()->get($name)
        );

        $this->getEventDispatcher()->dispatch(Event\ConfigureMenuEvent::CONFIGURE, new Event\ConfigureMenuEvent($factory, $menu));

        return $menu;
    }

    /**
     * {@inheritDoc}
     */
    public function has($name, array $options = array())
    {
        return $this->getGenerator()->has($name);
    }

    /**
     * Sets options.
     *
     * @param array $options An array of options
     *
     * @throws \InvalidArgumentException When unsupported option is provided
     */
    public function setOptions(array $options)
    {
        $this->options = array(
            'cache_dir'              => null,
            'debug'                  => false,
            'generator_class'        => 'BDev\\Bundle\\RoutingExtraBundle\\Extension\\KnpMenu\\Generator\\MenuGenerator',
            'generator_base_class'   => 'BDev\\Bundle\\RoutingExtraBundle\\Extension\\KnpMenu\\Generator\\MenuGenerator',
            'generator_dumper_class' => 'BDev\\Bundle\\RoutingExtraBundle\\Extension\\KnpMenu\\Generator\\Dumper\\PhpMenuGeneratorDumper',
            'generator_cache_class'  => 'ProjectMenuGenerator',
            'resource_type'          => null
        );

        // check option names and live merge, if errors are encountered Exception will be thrown
        $invalid = array();
        foreach ($options as $key => $value) {
            if (array_key_exists($key, $this->options)) {
                $this->options[$key] = $value;
            } else {
                $invalid[] = $key;
            }
        }

        if ($invalid) {
            throw new \InvalidArgumentException(sprintf('The MenuProvider does not support the following options: "%s".', implode('\', \'', $invalid)));
        }
    }

    /**
     * Sets an option.
     *
     * @param string $key   The key
     * @param mixed  $value The value
     *
     * @throws \InvalidArgumentException
     */
    public function setOption($key, $value)
    {
        if (!array_key_exists($key, $this->options)) {
            throw new \InvalidArgumentException(sprintf('The MenuProvider does not support the "%s" option.', $key));
        }

        $this->options[$key] = $value;
    }

    /**
     * Gets an option value.
     *
     * @param string $key The key
     * @return mixed The value
     *
     * @throws \InvalidArgumentException
     */
    public function getOption($key)
    {
        if (!array_key_exists($key, $this->options)) {
            throw new \InvalidArgumentException(sprintf('The Router does not support the "%s" option.', $key));
        }

        return $this->options[$key];
    }

    /**
     * {@inheritDoc}
     */
    public function warmUp($cacheDir)
    {
        $currentDir = $this->getOption('cache_dir');

        $this->setOption('cache_dir', $cacheDir);
        $this->getGenerator();

        $this->setOption('cache_dir', $currentDir);
    }

    /**
     * @return \BDev\Bundle\RoutingExtraBundle\Extension\KnpMenu\Generator\MenuGeneratorInterface
     */
    protected function getGenerator()
    {
        if ($this->generator !== null) {
            return $this->generator;
        }

        if ($this->options['cache_dir'] === null || $this->options['generator_cache_class'] === null) {
            $this->generator = new $this->options['generator_class']($this->getMenuCollection(), $this->getCompiler());
        } else {
            $class = $this->options['generator_cache_class'];
            $cache = new ConfigCache($this->options['cache_dir'].'/'.$class.'.php', $this->options['debug']);
            if (!$cache->isFresh($class)) {
                /** @var Generator\Dumper\MenuGeneratorDumperInterface $dumper  */
                $dumper = new $this->options['generator_dumper_class']($this->getMenuCollection(), $this->getCompiler());

                $options = array(
                    'class'      => $class,
                    'base_class' => $this->options['generator_base_class'],
                );

                $cache->write($dumper->dump($options), $this->getMenuCollection()->getResources());
            }

            require_once $cache;

            $this->generator = new $class();
        }

        if ($this->generator instanceof ContainerAwareInterface) {
            $this->generator->setContainer($this->container);
        }
        return $this->generator;
    }

    /**
     * @return RawMenuCollection
     */
    protected function getMenuCollection()
    {
        if ($this->collection === null) {
            $loader = $this->container->get('bdev_routing_extra.knp_menu.loader');
            $this->collection = $loader->load($this->resource, $this->getOption('resource_type'));
        }
        return $this->collection;
    }

    /**
     * @return MenuCompilerInterface
     */
    protected function getCompiler()
    {
        return $this->container->get('bdev_routing_extra.knp_menu.compiler');
    }

    /**
     * @return \Knp\Menu\FactoryInterface
     */
    protected function getFactory()
    {
        return $this->container->get('knp_menu.factory');
    }

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected function getEventDispatcher()
    {
        return $this->container->get('event_dispatcher');
    }
}