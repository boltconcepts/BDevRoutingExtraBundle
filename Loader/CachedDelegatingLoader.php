<?php
namespace BDev\Bundle\RoutingExtraBundle\Loader;

use Symfony\Component\Config\Loader\DelegatingLoader;

/**
 * CachedDelegatingLoader delegates loading to other loaders using a loader resolver and
 * stores the result for reuse.
 */
class CachedDelegatingLoader extends DelegatingLoader
{
    protected $cache = array();

    /**
     * {inheritDoc}
     */
    public function load($resource, $type = null)
    {
        $key = $this->generateCacheKey($resource, $type);
        if (!isset($this->cache[$key])) {
            $this->cache[$key] = parent::load($resource, $type);
        }
        return $this->cache[$key];
    }

    /**
     * Generate a cache key for the given resource.
     *
     * @param mixed $resource
     * @param null|string $type
     * @return string
     */
    protected function generateCacheKey($resource, $type = null)
    {
        $type = $type === null ? '' : '_' . (string)$type;
        if ($resource instanceof \SplFileInfo) {
            $resource = $resource->getRealPath();
        }

        if (is_object($resource)) {
            return spl_object_hash($resource) . $type;
        }
        if (is_array($resource)) {
            return sha1(json_encode($resource)) . $type;
        }
        return sha1((string)$resource) . $type;
    }
}