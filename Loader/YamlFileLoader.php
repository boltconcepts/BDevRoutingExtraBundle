<?php
namespace BDev\Bundle\RoutingExtraBundle\Loader;

use BDev\Bundle\RoutingExtraBundle\RawRoute;
use BDev\Bundle\RoutingExtraBundle\RawRouteCollection;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Loader\FileLoader;

class YamlFileLoader extends FileLoader
{
    /**
     * {@inheritdoc}
     */
    public function load($file, $type = null)
    {
        $path = $this->locator->locate($file);

        $configuration = Yaml::parse($path);

        $collection = new RawRouteCollection();
        $collection->addResource(new FileResource($path));

        // empty file
        if ($configuration === null) {
            return $collection;
        }

        // not an array
        if (!is_array($configuration)) {
            throw new \InvalidArgumentException(sprintf('The file "%s" must contain a YAML array.', $path));
        }

        foreach ($configuration as $name => $config) {
            if (isset($config['resource'])) {
                $this->parseImport($collection, $config, $path, $file);
            } else {
                $this->parseRoute($collection, $name, $config, $path);
            }
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yml' === pathinfo($resource, PATHINFO_EXTENSION) && (!$type || 'yaml' === $type);
    }

    /**
     * Parses a route and adds it to the RawRouteCollection.
     *
     * @param RawRouteCollection    $collection UI Collection
     * @param string                $name       Route name
     * @param array                 $config     Route definition
     * @param string                $path       Full path of the YAML file being processed
     */
    protected function parseRoute(RawRouteCollection $collection, $name, array $config, $path)
    {
        $collection->add(new RawRoute($name, $config));
    }

    /**
     * Parses an import and adds the routes in the resource to the RawRouteCollection.
     *
     * @param RawRouteCollection    $collection A RawRouteCollection instance
     * @param array                 $config     Route definition
     * @param string                $path       Full path of the YAML file being processed
     * @param string                $file       Loaded file name
     */
    protected function parseImport(RawRouteCollection $collection, array $config, $path, $file)
    {
        $type = isset($config['type']) ? $config['type'] : null;

        $this->setCurrentDir(dirname($path));

        $subCollection = $this->import($config['resource'], $type, false, $file);

        $collection->addCollection($subCollection);
    }
}