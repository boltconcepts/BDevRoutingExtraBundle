<?php
namespace BDev\Bundle\RoutingExtraBundle\CacheWarmer;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;

class WarmableCacheWarmer implements CacheWarmerInterface
{
    /**
     * @var WarmableInterface[]
     */
    protected $warmers;

    public function __construct(array $warmers = array())
    {
        $this->setWarmers($warmers);
    }

    /**
     * {@inheritDoc}
     */
    public function warmUp($cacheDir)
    {
        foreach ($this->warmers as $warmer) {
            $warmer->warmUp($cacheDir);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isOptional()
    {
        return true;
    }

    public function setWarmers(array $warmers)
    {
        $this->warmers = array();
        foreach ($warmers as $warmer) {
            $this->add($warmer);
        }
    }

    public function add(WarmableInterface $warmer)
    {
        $this->warmers[] = $warmer;
    }
}