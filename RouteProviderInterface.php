<?php
namespace BDev\Bundle\RoutingExtraBundle;

interface RouteProviderInterface
{
    /**
     * @return RouteCollectionInterface
     */
    public function getCollection();
}