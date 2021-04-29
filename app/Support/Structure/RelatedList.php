<?php

namespace Uccello\Core\Support\Structure;

class RelatedList
{
    private $data;

    /**
     * Constructure
     *
     * @param \stdclass $data
     */
    public function __construct($data = null)
    {
        if ($data === null || is_object($data)) {
            $this->data = $data;
        } else {
            throw 'First argument must be an object';
        }
    }

    /**
     * Getter to retrieve an attribute from $data.
     *
     * @param string $attribute
     *
     * @return mixed
     */
    public function __get(string $attribute)
    {
        return optional($this->data)->{$attribute};
    }

    /**
     * Setter to update an attribute into $data.
     *
     * @param string $attribute
     * @param mixed $value
     */
    public function __set(string $attribute, $value)
    {
        $this->data[$attribute] = $value;
    }
}
