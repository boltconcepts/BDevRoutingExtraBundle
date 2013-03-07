<?php
namespace BDev\Bundle\RoutingExtraBundle\Tests;

use BDev\Bundle\RoutingExtraBundle\RouteCollection;

class RouteCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRoute()
    {
        $route = $this->getMockBuilder('BDev\Bundle\RoutingExtraBundle\Route')
            ->disableOriginalConstructor()
            ->getMock();

        $compiler = $this->getMock('BDev\Bundle\RoutingExtraBundle\RouteCompilerInterface');
        $compiler->expects($this->once())
            ->method('compile')
            ->will($this->returnValue($route));

        $rawRoute = $this->getMockBuilder('BDev\Bundle\RoutingExtraBundle\RawRoute')
            ->disableOriginalConstructor()
            ->getMock();
        $rawRoute->expects($this->once())
            ->method('getRoute')
            ->will($this->returnValue('my_route'));

        $collection = new RouteCollection(array($rawRoute), $compiler);

        $this->assertEquals($route, $collection->getRoute('my_route'));
        $this->assertNull($collection->getRoute('no_route'));
    }

    public function testGetParent()
    {
        $route1 = $this->getMockBuilder('BDev\Bundle\RoutingExtraBundle\Route')
            ->disableOriginalConstructor()
            ->getMock();
        $route1->expects($this->atLeastOnce())
            ->method('getParent')
            ->will($this->returnValue('route_parent'));

        $route2 = $this->getMockBuilder('BDev\Bundle\RoutingExtraBundle\Route')
            ->disableOriginalConstructor()
            ->getMock();
        $route2->expects($this->atLeastOnce())
            ->method('getParent')
            ->will($this->returnValue(null));

        $rawRoute1 = $this->getMockBuilder('BDev\Bundle\RoutingExtraBundle\RawRoute')
            ->disableOriginalConstructor()
            ->getMock();
        $rawRoute1->expects($this->once())
            ->method('getRoute')
            ->will($this->returnValue('my_route'));

        $rawRoute2 = $this->getMockBuilder('BDev\Bundle\RoutingExtraBundle\RawRoute')
            ->disableOriginalConstructor()
            ->getMock();
        $rawRoute2->expects($this->once())
            ->method('getRoute')
            ->will($this->returnValue('route_parent'));

        $compiler = $this->getMock('BDev\Bundle\RoutingExtraBundle\RouteCompilerInterface');
        $compiler->expects($this->exactly(2))
            ->method('compile')
            ->will(
                $this->returnValueMap(
                    array(
                        array($rawRoute1, $route1),
                        array($rawRoute2, $route2)
                    )
                )
            );

        $collection = new RouteCollection(array($rawRoute1, $rawRoute2), $compiler);

        $this->assertEquals($route2, $collection->getParent('my_route'));
        $this->assertNull($collection->getParent('route_parent'));
    }
}