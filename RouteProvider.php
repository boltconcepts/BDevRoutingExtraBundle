<?php
namespace BDev\Bundle\RoutingExtraBundle;

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;

class RouteProvider implements WarmableInterface
{
    protected $options;

    /**
     * @var mixed
     */
    protected $resource;

    protected $container;

    protected $collection;

    protected $rawCollection;

    public function __construct(ContainerInterface $container, $resource, array $options = array())
    {
        $this->container = $container;
        $this->resource = $resource;
        $this->setOptions($options);
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
            'collection_class'       => 'BDev\\Bundle\\RoutingExtraBundle\\RouteCollection',
            'collection_base_class'  => 'BDev\\Bundle\\RoutingExtraBundle\\RouteCollection',
            'collection_dumper_class'=> 'BDev\\Bundle\\RoutingExtraBundle\\Dumper\\PhpCollectionDumper',
            'collection_cache_class' => 'ProjectRoutingExtraCollection',
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
            throw new \InvalidArgumentException(sprintf('The RouteProvider does not support the following options: "%s".', implode('\', \'', $invalid)));
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
            throw new \InvalidArgumentException(sprintf('The RouteProvider does not support the "%s" option.', $key));
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
            throw new \InvalidArgumentException(sprintf('The RouteProvider does not support the "%s" option.', $key));
        }

        return $this->options[$key];
    }

    public function getCollection()
    {
        if (null !== $this->collection) {
            return $this->collection;
        }

        if (null === $this->options['cache_dir'] || null === $this->options['collection_cache_class']) {
            $this->collection = new $this->options['collection_class']($this->getRawCollection()->all(), $this->getCompiler());
        } else {
            $class = $this->options['collection_cache_class'];
            $cache = new ConfigCache($this->options['cache_dir'].'/'.$class.'.php', $this->options['debug']);
            if (!$cache->isFresh($class)) {
                /** @var $dumper Dumper\CollectionDumperInterface */
                $dumper = new $this->options['collection_dumper_class']($this->getRawCollection(), $this->getCompiler());

                $options = array(
                    'class'      => $class,
                    'base_class' => $this->options['collection_base_class'],
                );

                $cache->write($dumper->dump($options), $this->getRawCollection()->getResources());
            }

            require_once $cache;

            $this->collection = new $class();
        }

        if ($this->collection instanceof ContainerAwareInterface) {
            $this->collection->setContainer($this->container);
        }
        return $this->collection;
    }

    /**
     * {@inheritDoc}
     */
    public function warmUp($cacheDir)
    {
        $currentDir = $this->getOption('cache_dir');

        $this->setOption('cache_dir', $cacheDir);
        $this->getCollection();

        $this->setOption('cache_dir', $currentDir);
    }

    /**
     * @return RawRouteCollection
     */
    protected function getRawCollection()
    {
        if ($this->rawCollection === null) {
            $loader = $this->container->get('bdev_routing_extra.loader');
            $this->rawCollection = $loader->load($this->resource, $this->getOption('resource_type'));
        }
        return $this->rawCollection;
    }

    /**
     * @return RouteCompilerInterface
     */
    protected function getCompiler()
    {
        return $this->container->get('bdev_routing_extra.compiler');
    }
}