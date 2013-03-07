<?php
namespace BDev\Bundle\RoutingExtraBundle\Extension\KnpMenu\Generator;

use BDev\Bundle\RoutingExtraBundle\Extension\KnpMenu\Exception\MenuNotFoundException;
use BDev\Bundle\RoutingExtraBundle\Extension\KnpMenu\MenuCompilerInterface;
use BDev\Bundle\RoutingExtraBundle\Extension\KnpMenu\RawMenuCollection;

class MenuGenerator
{
    protected $collection;

    protected $compiler;

    public function __construct(RawMenuCollection $collection, MenuCompilerInterface $compiler)
    {
        $this->collection = $collection;
        $this->compiler = $compiler;
    }

    public function get($name)
    {
        $menu = $this->collection->get($name);
        if ($menu === null) {
            throw new MenuNotFoundException(sprintf('Menu "%s" does not exist.', $name));
        }
        return $this->compiler->compile($menu);
    }

    public function has($name)
    {
        return $this->collection->get($name) !== null;
    }
}