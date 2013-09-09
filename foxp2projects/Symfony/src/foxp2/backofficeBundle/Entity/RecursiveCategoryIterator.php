<?php

namespace foxp2\backofficeBundle\Entity;

use foxp2\backofficeBundle\Entity\Categories;
use Doctrine\Common\Collections\Collection;

class RecursiveCategoryIterator implements \RecursiveIterator
{

    private $_data;

    public function __construct(Collection $data)
    {
        $this->_data = $data;
    }

    public function hasChildren()
    {
        return ( ! $this->_data->current()->getChildren()->isEmpty());
    }

    public function getChildren()
    {
        return new RecursiveCategoryIterator($this->_data->current()->getChildren());
    }

    public function current()
    {
        return $this->_data->current();
    }

    public function next()
    {
        $this->_data->next();
    }

    public function key()
    {
        return $this->_data->key();
    }

    public function valid()
    {
        return $this->_data->current() instanceof Categories;
    }

    public function rewind()
    {
        $this->_data->first();
    }
}
?>