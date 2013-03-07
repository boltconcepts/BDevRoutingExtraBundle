<?php
namespace BDev\Bundle\RoutingExtraBundle\Extension\KnpMenu\Generator\Dumper;

interface MenuGeneratorDumperInterface
{
    /**
     * Dumps a set of menus to a string representation of executable code
     * that can then be used to generate the menus.
     *
     * @param array $options An array of options
     *
     * @return string Executable code
     */
    public function dump(array $options = array());

    /**
     * Gets the collection to dump.
     *
     * @return \BDev\Bundle\RoutingExtraBundle\Extension\KnpMenu\RawMenuCollection A RawMenuCollection instance
     */
    public function getCollection();
}