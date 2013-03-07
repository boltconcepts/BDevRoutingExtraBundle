<?php
namespace BDev\Bundle\RoutingExtraBundle;

interface RouteCompilerInterface
{
    /**
     * Compiles the current route instance.
     *
     * @param RawRoute $route A Route instance
     *
     * @return Route A CompiledRoute instance
     */
    public function compile(RawRoute $route);
}