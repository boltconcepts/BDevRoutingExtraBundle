<?php
namespace BDev\Bundle\RoutingExtraBundle;

class RouteCollection implements RouteCollectionInterface
{
    private $compiler;

    private $routes;

    public function __construct(array $routes, RouteCompilerInterface $compiler)
    {
        $this->compiler = $compiler;
        $this->setRoutes($routes);
    }

    /**
     * @param string $route
     * @return Route|null The route instance or NULL
     */
    public function getRoute($route)
    {
        return isset($this->routes[$route]) ? $this->routes[$route] : null;
    }

    /**
     * @param string $route The route child
     * @return Route The parent of the given route
     */
    public function getParent($route)
    {
        $route = $this->getRoute($route);
        if ($route !== null && $route->getParent() !== null) {
            return $this->getRoute($route->getParent());
        }
        return null;
    }

    private function setRoutes(array $routes)
    {
        $this->routes = array();
        foreach ($routes as $route) {
            $this->addRoute($route);
        }
    }

    private function addRoute(RawRoute $route)
    {
        $this->routes[$route->getRoute()] = $this->compiler->compile($route);
    }
}