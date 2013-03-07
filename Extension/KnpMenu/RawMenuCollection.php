<?php
namespace BDev\Bundle\RoutingExtraBundle\Extension\KnpMenu;

use Symfony\Component\Config\Resource\ResourceInterface;

class RawMenuCollection
{

    /**
     * @var ResourceInterface[]
     */
    protected $resources = array();

    /**
     * @var RawMenu[]
     */
    protected $menus = array();

    /**
     * Adds a menu.
     *
     * @param RawMenu $menu
     */
    public function add(RawMenu $menu)
    {
        $name = $menu->getName();

        unset($this->menus[$name]);

        $this->menus[$name] = $menu;
    }

    /**
     * Gets a menu by name.
     *
     * @param string $name The menu name
     *
     * @return RawMenu|null A Menu instance or null when not found
     */
    public function get($name)
    {
        return isset($this->menus[$name]) ? $this->menus[$name] : null;
    }

    /**
     * Returns all menus in this collection.
     *
     * @return RawMenu[] An array of menus
     */
    public function all()
    {
        return $this->menus;
    }

    /**
     * Returns an array of resources loaded to build this collection.
     *
     * @return ResourceInterface[] An array of resources
     */
    public function getResources()
    {
        return array_unique($this->resources);
    }

    /**
     * Adds a resource for this collection.
     *
     * @param ResourceInterface $resource A resource instance
     */
    public function addResource(ResourceInterface $resource)
    {
        $this->resources[] = $resource;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->menus);
    }
}