<?php
namespace BDev\Bundle\RoutingExtraBundle\Tests;

use BDev\Bundle\RoutingExtraBundle\Route;

class RawRouteTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $options = array('option' => '');
        $route = new Route('my_route', $options, 'my_parent');

        $this->assertEquals('my_route', $route->getRoute());
        $this->assertEquals($options, $route->getOptions());
        $this->assertEquals('my_parent', $route->getParent());
    }
}