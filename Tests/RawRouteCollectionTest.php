<?php
namespace BDev\Bundle\RoutingExtraBundle\Tests;

use BDev\Bundle\RoutingExtraBundle\RawRouteCollection;

class RawRouteCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testBasics()
    {
        $route = $this->getMockBuilder('BDev\Bundle\RoutingExtraBundle\RawRoute')
            ->disableOriginalConstructor()
            ->getMock();
        $route->expects($this->once())
            ->method('getRoute')
            ->will($this->returnValue('my_route'));

        $collection = new RawRouteCollection();

        $this->assertCount(0, $collection->all());
        $this->assertNull($collection->get('my_route'));

        $collection->add($route);

        $this->assertCount(1, $collection->all());
        $this->assertEquals($route, $collection->get('my_route'));

        $collection->remove('my_route');

        $this->assertCount(0, $collection->all());
        $this->assertNull($collection->get('my_route'));
    }

    public function testAddDuplicate()
    {
        $route1 = $this->getMockBuilder('BDev\Bundle\RoutingExtraBundle\RawRoute')
            ->disableOriginalConstructor()
            ->getMock();
        $route1->expects($this->once())
            ->method('getRoute')
            ->will($this->returnValue('my_route'));

        $route2 = $this->getMockBuilder('BDev\Bundle\RoutingExtraBundle\RawRoute')
            ->disableOriginalConstructor()
            ->getMock();
        $route2->expects($this->once())
            ->method('getRoute')
            ->will($this->returnValue('my_route'));

        $collection = new RawRouteCollection();
        $collection->add($route1);

        $this->assertCount(1, $collection->all());
        $this->assertEquals($route1, $collection->get('my_route'));

        $collection->add($route2);

        $this->assertCount(1, $collection->all());
        $this->assertEquals($route2, $collection->get('my_route'));
    }

    public function testAddCollection()
    {
        $route1 = $this->getMockBuilder('BDev\Bundle\RoutingExtraBundle\RawRoute')
            ->disableOriginalConstructor()
            ->getMock();
        $route1->expects($this->once())
            ->method('getRoute')
            ->will($this->returnValue('my_route_1'));

        $route2 = $this->getMockBuilder('BDev\Bundle\RoutingExtraBundle\RawRoute')
            ->disableOriginalConstructor()
            ->getMock();
        $route2->expects($this->atLeastOnce())
            ->method('getRoute')
            ->will($this->returnValue('my_route_2'));

        $collection1 = new RawRouteCollection();
        $collection1->add($route1);

        $collection2 = new RawRouteCollection();
        $collection2->add($route2);

        $collection1->addCollection($collection2);

        $this->assertCount(2, $collection1->all());
        $this->assertEquals($route1, $collection1->get('my_route_1'));
        $this->assertEquals($route2, $collection1->get('my_route_2'));

        $this->assertCount(1, $collection2->all());
        $this->assertEquals($route2, $collection2->get('my_route_2'));
    }

    public function testResource()
    {
        $resource1 = $this->getMock('Symfony\Component\Config\Resource\ResourceInterface');
        $resource2 = $this->getMock('Symfony\Component\Config\Resource\ResourceInterface');

        $collection = new RawRouteCollection();
        $this->assertCount(0, $collection->getResources());

        $collection->addResource($resource1);
        $this->assertCount(1, $collection->getResources());

        $collection->addResource($resource2);
        $this->assertCount(2, $collection->getResources());
    }
}