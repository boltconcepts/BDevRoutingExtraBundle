<?php
namespace BDev\Bundle\RoutingExtraBundle\Extension\KnpMenu\Loader;

use BDev\Bundle\RoutingExtraBundle\Extension\KnpMenu\RawMenuCollection;
use BDev\Bundle\RoutingExtraBundle\Extension\KnpMenu\RawMenu;
use BDev\Bundle\RoutingExtraBundle\RawRoute;
use BDev\Bundle\RoutingExtraBundle\RawRouteCollection;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * MenuDelegatingLoader delegates loading to a (UI) loader and compiles the result into a MenuCollection instance.
 */
class MenuDelegatingLoader extends Loader
{
    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * @var array
     */
    protected $menus;

    /**
     * Constructor.
     *
     * @param LoaderInterface $loader A LoaderInterface to refer the loading to.
     * @param array $menus A array with menu options.
     */
    public function __construct(LoaderInterface $loader, array $menus = array())
    {
        $this->loader = $loader;
        $this->menus = $menus;
    }

    /**
     * {@inheritDoc}
     */
    public function load($resource, $type = null)
    {
        $collection = $this->loader->load($resource, $type);
        if ($collection instanceof RawMenuCollection) {
            return $collection;
        }
        if ($collection instanceof RawRouteCollection) {
            return $this->parseRoutes($collection);
        }
        throw new \RuntimeException('Unsupported result from loader');
    }

    /**
     * {@inheritDoc}
     */
    public function supports($resource, $type = null)
    {
        return $this->loader->supports($resource, $type);
    }

    /**
     * Map the given RawRouteCollection instance to a MenuCollection instance.
     *
     * @param RawRouteCollection $routeCollection
     * @return RawMenuCollection
     */
    protected function parseRoutes(RawRouteCollection $routeCollection)
    {
        $collection = new RawMenuCollection();

        foreach ($routeCollection->getResources() as $resource) {
            $collection->addResource($resource);
        }

        $routes = $routeCollection->all();
        foreach ($routes as $route) {
            $this->parseRoute($collection, $route);
        }

        return $collection;
    }

    protected function parseRoute(RawMenuCollection $collection, RawRoute $route)
    {
        $config = $route->getOptions();

        // Add menu's
        if (!isset($config['menu'])) {
            return;
        }

        foreach ($config['menu'] as $name => $item) {
            if (is_int($name) && !isset($item['menu'])) {
                throw new \RuntimeException('Invalid menu item no name was specified.');
            }
            $name = is_int($name) ? $item['menu'] : $name;
            $menu = $this->getMenu($collection, $name);

            $item = $item === null ? array() : $item;
            $this->parseMenuItem($menu, $route, $item);
        }
    }

    protected function parseMenuItem(RawMenu $menu, RawRoute $route, $config)
    {
        if (is_string($config)) {
            $config = array('label' => $config);
        }
        $data = array(
            'name' => isset($config['name']) ? $config['name'] : $route->getRoute(),
            'parent' => $this->getRouteConfigOption($route, $config, 'parent'),
            'route' => $route->getRoute(),
            'routeParameters' => isset($config['parameters']) ? $config['parameters'] : array(),
            'label' => $this->getRouteConfigOption($route, $config, 'label'),
            'labelAttributes' => isset($config['label_attributes']) ? $config['label_attributes'] : array(),
            'attributes' => isset($config['attributes']) ? $config['attributes'] : array(),
            'linkAttributes' => isset($config['link_attributes']) ? $config['link_attributes'] : array(),
            'childrenAttributes' => isset($config['children_attributes']) ? $config['children_attributes'] : array(),
            'display' => $this->getRouteConfigOption($route, $config, 'display', true),
        );
        $menu->addItem($data);
    }

    protected function getRouteConfigOption(RawRoute $route, array $config, $key, $default = null)
    {
        if (isset($config[$key])) {
            return $config[$key];
        }
        $routeConfig = $route->getOptions();
        if (isset($routeConfig[$key])) {
            return $routeConfig[$key];
        }
        return $default;
    }

    protected function getMenu(RawMenuCollection $collection, $name)
    {
        $menu = $collection->get($name);
        if ($menu === null) {
            $menuOptions = isset($this->menus[$name]) ? $this->menus[$name] : array();
            $menu = new RawMenu($name, $menuOptions);
            $collection->add($menu);
        }
        return $menu;
    }
}