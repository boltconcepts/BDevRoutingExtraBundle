<?php
namespace BDev\Bundle\RoutingExtraBundle\Tests;

use BDev\Bundle\RoutingExtraBundle\RawRouteCollection;
use BDev\Bundle\RoutingExtraBundle\RouteProvider;
use Symfony\Component\DependencyInjection\Container;

class RouteProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    protected $container;

    public function setUp()
    {
        $this->container = new Container();
    }

    public function tearDown()
    {
        $this->container = null;
    }

    public function testGetCollectionWithoutCache()
    {
        $this->container->set('bdev_routing_extra.compiler', $this->getMock('BDev\Bundle\RoutingExtraBundle\RouteCompilerInterface'));

        $loader = $this->getMock('Symfony\Component\Config\Loader\LoaderInterface');
        $loader->expects($this->once())->method('load')->with('fake', 'mock')->will($this->returnValue(new RawRouteCollection()));
        $this->container->set('bdev_routing_extra.loader', $loader);

        $collection = $this->getProvider('fake', array('cache_dir' => sys_get_temp_dir(), 'collection_cache_class' => null, 'resource_type' => 'mock'))
            ->getCollection();

        $this->assertInstanceOf('BDev\\Bundle\\RoutingExtraBundle\\RouteCollection', $collection);
    }

    public function testGetCollectionWithCache()
    {
        @unlink(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'MockCollectionCacheClass.php');

        $this->container->set('bdev_routing_extra.compiler', $this->getMock('BDev\Bundle\RoutingExtraBundle\RouteCompilerInterface'));

        $loader = $this->getMock('Symfony\Component\Config\Loader\LoaderInterface');
        $loader->expects($this->once())->method('load')->with('fake', 'mock')->will($this->returnValue(new RawRouteCollection()));
        $this->container->set('bdev_routing_extra.loader', $loader);

        $collection = $this->getProvider(
            'fake',
            array(
                'cache_dir' => sys_get_temp_dir(),
                'collection_cache_class' => 'MockCollectionCacheClass',
                'resource_type' => 'mock'
            )
        )->getCollection();

        $this->assertInstanceOf('MockCollectionCacheClass', $collection);

        $collection = $this->getProvider(
            'fake',
            array(
                'cache_dir' => sys_get_temp_dir(),
                'collection_cache_class' => 'MockCollectionCacheClass',
                'resource_type' => 'mock'
            )
        )->getCollection();

        $this->assertInstanceOf('MockCollectionCacheClass', $collection);
    }


    public function testSetOptions()
    {
        $options = array(
            'cache_dir'              => __DIR__,
            'debug'                  => true,
            'collection_class'       => 'MyRouteCollection',
            'collection_base_class'  => 'MyRouteCollection',
            'collection_dumper_class'=> 'MyPhpCollectionDumper',
            'collection_cache_class' => 'MyRoutingExtraCollection',
            'resource_type'          => 'yml'
        );

        $provider = new RouteProvider($this->container, null);
        $provider->setOptions($options);

        $this->assertAttributeEquals($options, 'options', $provider);
    }

    public function testSetOption()
    {
        $provider = $this->getProvider();
        $this->assertEquals(null, $provider->getOption('cache_dir'));

        $provider->setOption('cache_dir', __DIR__);
        $this->assertEquals(__DIR__, $provider->getOption('cache_dir'));
    }

    public function testGetOption()
    {
        $this->assertEquals('BDev\\Bundle\\RoutingExtraBundle\\RouteCollection', $this->getProvider()->getOption('collection_class'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetOptionsInvalid()
    {
        $this->getProvider()->setOptions(
            array(
                'cache_dir' => __DIR__,
                'evil_cache_dir' => 'i don\'t exists'
            )
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetOptionInvalid()
    {
        $this->getProvider()->setOption('evil_cache_dir', 'i don\'t exists');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetOptionInvalid()
    {
        $this->getProvider()->getOption('evil_cache_dir');
    }

    /**
     * @return \BDev\Bundle\RoutingExtraBundle\RouteProvider
     */
    protected function getProvider($resource = null, array $options = array())
    {
        return new RouteProvider($this->container, $resource, $options);
    }
}