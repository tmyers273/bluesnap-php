<?php

namespace Bluesnap\Models;

use Bluesnap\Utility;

class Model
{
    const COLLECTION = 'collection';
    const ITEM = 'item';

    /**
     * @var array $children
     */
    protected $children = [];

    /**
     * Model constructor.
     * @param object $data
     */
    public function __construct($data)
    {
        $data = Utility::objectToArray($data);

        if (is_array($data) && count($data))
        {
            foreach ($data as $key => $value)
            {
                if (!is_array($value))
                {
                    $value = html_entity_decode($value);
                }

                $this->$key = $value;
            }
        }

        $this->_loadChildren();
    }

    private function _loadChildren()
    {
        if (count($this->children))
        {
            foreach ($this->children as $child => $type)
            {
                if (isset($this->$child))
                {
                    $this->$child = $this->_parseChild($child, $type);
                }
            }
        }
    }

    private function _parseChild($child, $type)
    {
        $class_name = '\\Bluesnap\\Models\\'. ucfirst($child);

        if ($type === 'collection')
        {
            $items = [];
            foreach ($this->$child as $item)
            {
                $items[] = new $class_name($item);
            }
            return $items;
        }

        return new $class_name($this->$child);
    }
}
