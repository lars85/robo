<?php

namespace LarsMalach\Robo\Model\Traits;

trait Properties
{
    /** @var array */
    protected $properties = [];

    public function getProperty(string $key)
    {
        $parts = explode('.', $key);
        $property = &$this->properties;
        foreach ($parts as $part) {
            if (!isset($property[$part])) {
                return null;
            }
            $property = &$property[$part];
        }
        return $property;
    }

    public function hasProperty(string $key): bool
    {
        return $this->getProperty($key) !== null;
    }

    public function setProperty(string $key, $value)
    {
        $parts = explode('.', $key);
        $property = &$this->properties;
        foreach ($parts as $part) {
            if (!isset($property[$part])) {
                $property[$part] = [];
            }
            $property = &$property[$part];
        }
        $property = $value;
    }
}