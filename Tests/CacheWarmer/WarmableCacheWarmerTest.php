<?php
namespace BDev\Bundle\RoutingExtraBundle\Tests\CacheWarmer;

use BDev\Bundle\RoutingExtraBundle\CacheWarmer\WarmableCacheWarmer;

class WarmableCacheWarmerTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructEmpty()
    {
        $warmer = new WarmableCacheWarmer();

        $this->assertAttributeEquals(array(), 'warmers', $warmer);
    }

    public function testConstruct()
    {
        $builder = $this->getMock('Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface');
        $builder->expects($this->never())->method('warmUp');

        $warmables = array($builder);

        $warmer = new WarmableCacheWarmer($warmables);
        $this->assertAttributeEquals($warmables, 'warmers', $warmer);
    }

    public function testAdd()
    {
        $builder = $this->getMock('Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface');
        $builder->expects($this->never())->method('warmUp');

        $warmer = new WarmableCacheWarmer();

        $warmer->add($builder);
        $this->assertAttributeEquals(array($builder), 'warmers', $warmer);

        $warmer->add($builder);
        $this->assertAttributeEquals(array($builder, $builder), 'warmers', $warmer);
    }

    public function testWarmUp()
    {
        $builder = $this->getMock('Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface');
        $builder->expects($this->exactly(2))->method('warmUp')->with(__DIR__);

        $warmer = new WarmableCacheWarmer(array($builder, $builder));
        $warmer->warmUp(__DIR__);
    }
}