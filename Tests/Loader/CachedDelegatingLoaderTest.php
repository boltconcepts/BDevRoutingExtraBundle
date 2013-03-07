<?php
namespace BDev\Bundle\RoutingExtraBundle\Tests\Loader;

use BDev\Bundle\RoutingExtraBundle\Loader\CachedDelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;

class CachedDelegatingLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getLoadResources
     */
    public function testLoad($resource)
    {
        $loader1 = $this->getMock('Symfony\Component\Config\Loader\LoaderInterface');
        $loader1->expects($this->once())->method('supports')->will($this->returnValue(true));
        $loader1->expects($this->once())->method('load')->will($this->returnValue('loaded'));

        $loader = new CachedDelegatingLoader(new LoaderResolver(array($loader1)));
        $rtn = $loader->load($resource);

        $this->assertEquals('loaded', $rtn);
        $this->assertAttributeCount(1, 'cache', $loader);
    }

    public function testMultipleLoad()
    {
        $resources = $this->getLoadResources();

        $loader1 = $this->getMock('Symfony\Component\Config\Loader\LoaderInterface');
        $loader1->expects($this->exactly(count($resources)))->method('supports')->will($this->returnValue(true));
        $loader1->expects($this->exactly(count($resources)))->method('load')->will($this->returnValue('loaded'));

        $loader = new CachedDelegatingLoader(new LoaderResolver(array($loader1)));

        foreach ($resources as $i => $v) {
            $rtn = $loader->load($v[0]);

            $this->assertEquals('loaded', $rtn);
            $this->assertAttributeCount($i+1, 'cache', $loader);
        }
    }

    public function getLoadResources()
    {
        return array(
            array('string'),
            array(array('array')),
            array(new \StdClass()),
            array(new \SplFileInfo(__FILE__))
        );
    }
}