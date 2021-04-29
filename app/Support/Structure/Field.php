<?php

namespace Uccello\Core\Support\Structure;

class Field
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

    /**
     * Checks if field is visible in a view.
     *
     * @param string $viewName
     *
     * @return boolean
     */
    public function isVisible(string $viewName)
    {
        $isVisible = false;

        if ($viewName === 'everywhere') {
            $isVisible = $this->isVisibleEverywhere();
        } elseif ($viewName === 'create') {
            $isVisible = $this->isVisibleInCreateView();
        } elseif ($viewName === 'edit') {
            $isVisible = $this->isVisibleInEditView();
        } elseif ($viewName === 'detail') {
            $isVisible = $this->isVisibleInDetailView();
        } elseif ($viewName === 'list') {
            $isVisible = $this->isVisibleInListView();
        } else {
            throw "View name $viewName is invalid";
        }

        return $isVisible;
    }

    /**
     * Check if field is visible everywhere
     *
     * @return boolean
     */
    public function isVisibleEverywhere()
    {
        return optional($this->data)->visible === true;
    }

    /**
     * Check if field is visible in create view.
     *
     * @return boolean
     */
    public function isVisibleInCreateView()
    {
        return $this->isVisibleEverywhere()
            ||
            (
                is_object($this->data->visible)
                &&
                optional($this->data->visible)->create === true
            );
    }

    /**
     * Check if field is visible in edit view.
     *
     * @return boolean
     */
    public function isVisibleInEditView()
    {
        return $this->isVisibleEverywhere()
            ||
            (
                is_object($this->data->visible)
                &&
                optional($this->data->visible)->edit === true
            );
    }

    /**
     * Check if field is visible in detail view.
     *
     * @return boolean
     */
    public function isVisibleInDetailView()
    {
        return $this->isVisibleEverywhere()
            ||
            (
                is_object($this->data->visible)
                &&
                optional($this->data->visible)->detail === true
            );
    }

    /**
     * Check if field is visible in list view.
     *
     * @return boolean
     */
    public function isVisibleInListView()
    {
        return $this->isVisibleEverywhere()
            ||
            (
                is_object($this->data->visible)
                &&
                optional($this->data->visible)->list === true
            );
    }
}