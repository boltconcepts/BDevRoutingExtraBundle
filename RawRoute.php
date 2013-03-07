<?php
namespace BDev\Bundle\RoutingExtraBundle;

class RawRoute
{
    /**
     * @var string
     */
    protected $route;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param string $route
     * @param array $options
     */
    public function __construct($route, array $options = array(), $parent = null)
    {
        $this->parent = $parent;
        $this->route = $route;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return string|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}