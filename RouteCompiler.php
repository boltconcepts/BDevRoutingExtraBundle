<?php
namespace BDev\Bundle\RoutingExtraBundle;

use BDev\Bundle\RoutingExtraBundle\RawRoute;
use BDev\Bundle\RoutingExtraBundle\Route;
use BDev\Bundle\RoutingExtraBundle\RouteCompilerInterface;
use Symfony\Component\Routing\RouterInterface;

class RouteCompiler implements RouteCompilerInterface
{
    protected $collection;

    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * Compiles the current route instance.
     *
     * @param RawRoute $routeInfo A Route instance
     *
     * @return Route A CompiledRoute instance
     */
    public function compile(RawRoute $routeInfo)
    {
        $route = $this->getCollection()->get($routeInfo->getRoute());
        if ($route === null) {
            throw new \RuntimeException(sprintf('Unable to load route `%s` from router RouteCollection', $routeInfo->getRoute()));
        }

        return new Route(
            $routeInfo->getRoute(),
            $routeInfo->getOptions(),
            $routeInfo->getParent(),
            array_keys($route->getRequirements())
        );
    }

    protected function getCollection()
    {
        return $this->router->getRouteCollection();
    }
}