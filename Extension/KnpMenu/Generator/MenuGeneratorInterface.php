<?php
namespace BDev\Bundle\RoutingExtraBundle\Extension\KnpMenu\Generator;

interface MenuGeneratorInterface
{
    public function get($name);

    public function has($name);
}