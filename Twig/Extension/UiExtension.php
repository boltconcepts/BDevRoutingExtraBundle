<?php
namespace BDev\Bundle\RoutingExtraBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_Extension;

class UiExtension extends Twig_Extension implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array(
            'route_extra' => new \Twig_Function_Method($this, 'getOption'),
            'route_parent' => new \Twig_Function_Method($this, 'getParent')
        );
    }

    /**
     * Retrieve a page option
     *
     * @param array|string $option
     * @param mixed $default
     * @param string|null $route
     * @return string
     */
    public function getOption($option, $default = null, $route = null)
    {
        if ($route === null) {
            $route = $this->getCurrentRoute();
        }

        $route = $this->getRouteCollection()->getRoute($route);
        if ($route === null) {
            return $default;
        }

        $option = (array)$option;
        foreach ($option as $opt) {
            if ($route->hasOption($opt)) {
                return $route->getOption($opt);
            }
        }
        return $default;
    }

    /**
     * Retrieve the parent route of the given page
     *
     * @param string|null $route
     * @return string
     */
    public function getParent($route = null)
    {
        if ($route === null) {
            $route = $this->getCurrentRoute();
        }

        $parentRoute = $this->getRouteCollection()->getParent($route);
        return $parentRoute === null ? null : $parentRoute->getRoute();
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'bdev_routing_extra';
    }

    /**
     * @return \BDev\Bundle\RoutingExtraBundle\RouteCollectionInterface
     */
    protected function getRouteCollection()
    {
        return $this->container->get('bdev_routing_extra.provider')->getCollection();
    }

    /**
     * @return string
     */
    protected function getCurrentRoute()
    {
        return $this->container->get('request')->get('_route');
    }
}