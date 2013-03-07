<?php
namespace BDev\Bundle\RoutingExtraBundle\Tests\Dumper;

use BDev\Bundle\RoutingExtraBundle\Dumper\PhpCollectionDumper;
use BDev\Bundle\RoutingExtraBundle\RouteCompiler;
use BDev\Bundle\RoutingExtraBundle\RawRoute;
use BDev\Bundle\RoutingExtraBundle\RawRouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class PhpCollectionDumperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RawRouteCollection
     */
    protected $collection;

    /**
     * @var PhpCollectionDumper
     */
    protected $collectionDumper;

    /**
     * @var RouteCollection
     */
    protected $routerCollection;

    /**
     * @var string
     */
    private $testTmpFilepath;

    protected function setUp()
    {
        parent::setUp();

        $this->routerCollection = new RouteCollection();

        $router = $this->getMock('Symfony\Component\Routing\RouterInterface');
        $router->expects($this->any())->method('getRouteCollection')->will($this->returnValue($this->routerCollection));

        $this->collection = new RawRouteCollection();
        $this->collectionDumper = new PhpCollectionDumper($this->collection, new RouteCompiler($router));
        $this->testTmpFilepath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'php_collection.php';

        @unlink($this->testTmpFilepath);
    }

    protected function tearDown()
    {
        parent::tearDown();

        @unlink($this->testTmpFilepath);

        $this->collection = null;
        $this->collectionDumper = null;
        $this->testTmpFilepath = null;
    }

    public function testDumpWithRoutes()
    {
        $this->collection->add(new RawRoute('my_route_1', array('label' => 'Route 1')));
        $this->collection->add(new RawRoute('my_child_route_1', array('label' => 'Route 2', 'summary' => 'My description'), 'my_route_1'));

        $this->routerCollection->add('my_route_1', new Route('/testing/{foo}', array(), array('foo' => '[0-9]+')));
        $this->routerCollection->add('my_child_route_1', new Route('/testing2'));

        file_put_contents($this->testTmpFilepath, $this->collectionDumper->dump());
        include ($this->testTmpFilepath);

        $projectCollection = new \ProjectRoutingExtraCollection();

        /** @var $myRoute \BDev\Bundle\RoutingExtraBundle\Route */
        $myRoute = $projectCollection->getRoute('my_route_1');

        $this->assertInstanceOf('BDev\Bundle\RoutingExtraBundle\Route', $myRoute);
        $this->assertEquals('Route 1', $myRoute->getOption('label'));
        $this->assertNull($myRoute->getOption('summary'));
        $this->assertEquals('my_route_1', $myRoute->getRoute());
        $this->assertEquals(array('foo'), $myRoute->getRouteRequirements());
        $this->assertNull($projectCollection->getParent('my_route_1'));
        $this->assertEquals($myRoute, $projectCollection->getParent('my_child_route_1'));

        /** @var $myChildRoute \BDev\Bundle\RoutingExtraBundle\Route */
        $myChildRoute = $projectCollection->getRoute('my_child_route_1');
        $this->assertEquals('my_child_route_1', $myChildRoute->getRoute());
        $this->assertEquals('Route 2', $myChildRoute->getOption('label'));
        $this->assertEquals('My description', $myChildRoute->getOption('summary'));
        $this->assertEquals(array(), $myChildRoute->getRouteRequirements());
        $this->assertEquals('my_child_route_1', $myChildRoute->getRoute());
    }

    public function testDumpWithoutRoutes()
    {
        file_put_contents($this->testTmpFilepath, $this->collectionDumper->dump(array('class' => 'WithoutRoutesCollection')));
        include ($this->testTmpFilepath);

        $projectCollection = new \WithoutRoutesCollection();

        $this->assertNull($projectCollection->getRoute('my_route_1'));
        $this->assertNull($projectCollection->getParent('my_route_1'));
    }
}