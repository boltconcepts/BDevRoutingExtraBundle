<?php
namespace BDev\Bundle\RoutingExtraBundle\Extension\KnpMenu;

class RawMenu
{
    protected $name;

    protected $items = array();

    public function __construct($name, array $options = array())
    {
        $this->name = $name;
        $this->options = array_merge(
            array(
                'attributes' => array(),
                'linkAttributes' => array(),
                'childrenAttributes' => array(),
            ),
            $options
        );
    }

    public function getName()
    {
        return $this->name;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function addItem(array $data)
    {
        if (!isset($data['name'])) {
            throw new \InvalidArgumentException('Menu item must contain a `name`.');
        }
        if (!isset($data['route'])) {
            throw new \InvalidArgumentException('Menu item must contain a `route`.');
        }
        $this->items[$data['name']] = array_merge(
            array(
                'parent' => null
            ),
            $data
        );
    }
}