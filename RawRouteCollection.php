<?php
namespace BDev\Bundle\RoutingExtraBundle;

use Symfony\Component\Config\Resource\ResourceInterface;
use Traversable;

class RawRouteCollection implements \IteratorAggregate
{
    /**
     * @var RawRoute[]
     */
    private $routes = array();

    /**
     * @var array
     */
    private $resources = array();

    /**
     * Adds a route.
     *
     * @param RawRoute $route Route instance
     */
    public function add(RawRoute $route)
    {
        $name = $route->getRoute();

        unset($this->routes[$name]);

        $this->routes[$name] = $route;
    }

    /**
     * Returns all routes in this collection.
     *
     * @return RawRoute[] An array of routes
     */
    public function all()
    {
        return $this->routes;
    }

    /**
     * Gets a route by name.
     *
     * @param string $route The route name
     *
     * @return RawRoute|null A Route instance or null when not found
     */
    public function get($route)
    {
        return isset($this->routes[$route]) ? $this->routes[$route] : null;
    }

    /**
     * Removes a route or an array of routes by name from the collection
     *
     * @param string|array $route The route name or an array of route names
     */
    public function remove($route)
    {
        foreach ((array) $route as $n) {
            unset($this->routes[$n]);
        }
    }

    /**
     * Adds a route collection at the end of the current set by appending all
     * routes of the added collection.
     *
     * @param RawRouteCollection $collection  A RawRouteCollection instance
     *
     * @api
     */
    public function addCollection(RawRouteCollection $collection)
    {
        foreach ($collection->all() as $route) {
            $this->add($route);
        }

        $this->resources = array_merge($this->resources, $collection->getResources());
    }

    /**
     * Returns an array of resources loaded to build this collection.
     *
     * @return ResourceInterface[] An array of resources
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * Adds a resource for this collection.
     *
     * @param ResourceInterface $resource A resource instance
     */
    public function addResource(ResourceInterface $resource)
    {
        $this->resources[] = $resource;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->routes);
    }
}