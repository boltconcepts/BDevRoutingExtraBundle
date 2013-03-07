<?php
namespace BDev\Bundle\RoutingExtraBundle;

class Route extends RawRoute
{
    protected $routeRequirements;

    public function __construct($route, array $options = array(), $parent = null, array $routeRequirements = array())
    {
        parent::__construct($route, $options, $parent);
        $this->routeRequirements = $routeRequirements;
    }

    public function hasOption($option)
    {
        return isset($this->options[$option]);
    }

    public function getOption($option, $default = null)
    {
        return isset($this->options[$option]) ? $this->options[$option] : $default;
    }

    public function getRouteRequirements()
    {
        return $this->routeRequirements;
    }
}