<?php
namespace BDev\Bundle\RoutingExtraBundle;

interface RouteCollectionInterface
{
    /**
     * @param string $name
     * @return Route|null
     */
    public function getRoute($name);

    /**
     * @param string $name
     * @return Route|null
     */
    public function getParent($name);
}