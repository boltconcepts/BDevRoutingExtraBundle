<?php
namespace BDev\Bundle\RoutingExtraBundle\Extension\KnpMenu;

interface MenuCompilerInterface
{
    /**
     * Compile a menu to it's array representation that can be used by @see \Knp\Menu\MenuFactory
     *
     * @param RawMenu $menu
     * @return array
     */
    public function compile(RawMenu $menu);
}