<?php
namespace BDev\Bundle\RoutingExtraBundle\Dumper;

interface CollectionDumperInterface
{
    /**
     * Dumps a set of routes to a string representation of executable code.
     *
     * @param array $options An array of options
     *
     * @return string Executable code
     */
    public function dump(array $options = array());

    /**
     * Gets the collection to dump.
     *
     * @return \BDev\Bundle\RoutingExtraBundle\RawRouteCollection A RawRouteCollection instance
     */
    public function getCollection();
}