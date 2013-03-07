<?php
namespace BDev\Bundle\RoutingExtraBundle\Tests;

use BDev\Bundle\RoutingExtraBundle\Route;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $options = array('option' => '');
        $requirements = array('id');
        $route = new Route('my_route', $options, 'my_parent', $requirements);

        $this->assertEquals('my_route', $route->getRoute());
        $this->assertEquals($options, $route->getOptions());
        $this->assertEquals('my_parent', $route->getParent());
        $this->assertEquals($requirements, $route->getRouteRequirements());
    }
}