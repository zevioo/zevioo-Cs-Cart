<?php

/**
 * ZeviooAPI
 *Aln
 */

namespace ZeviooAPI;

abstract class ZeviooObject
{
    protected $zevioo;
    protected $zeviooObjectProperties = array();
    protected $initialObjectProperties = array();

    public function __construct($data = null, &$v = null)
    {
        $this->zevioo = $v;
        if ($data) {
            foreach ($data as $key => $value) {
                $this->zeviooObjectProperties[$key] = $value;
            }
            $this->initialObjectProperties = $this->zeviooObjectProperties;
        }
    }

    public function __set($key, $value)
    {
        $this->zeviooObjectProperties[$key] = $value;
    }
    public function __get($key)
    {
        if (array_key_exists($key, $this->zeviooObjectProperties)) {
            return $this->zeviooObjectProperties[$key];
        }

        return null;
    }

    public function __isset($key)
    {
        return isset($this->zeviooObjectProperties[$key]);
    }

    public function __unset($key)
    {
        unset($this->zeviooObjectProperties[$key]);
    }
    public function clear()
    {
        $this->zeviooObjectProperties = array();
    }
    public function toArray()
    {
        return $this->zeviooObjectProperties;
    }
    
    public function saveArray()
    {
    
        $output = $this->zeviooObjectProperties;
        foreach($output as $key => $value) {
            if ($key != 'id' && isset($this->initialObjectProperties[$key]) && $value == $this->initialObjectProperties[$key]) {
                unset($output[$key]);
            }
        }
        return $output;
    }
}
