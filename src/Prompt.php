<?php

namespace Ogi\Prompt;


class Prompt
{
    public function render()
    {
        $xml = '';

        $reflection = new \ReflectionObject($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            $name = $property->getName();
            $value = $property->getValue($this);
            $xml .= $this->toXml($name, $value);
        }

        return $xml;
    }

    private function toXml($name, $value)
    {
        if (is_array($value)) {
            $xml = "<{$name}>\n";
            foreach ($value as $key => $val) {
                if (is_array($val)) {
                    if (is_numeric($key)) {
                        // Numerical key with array value: wrap in <list>
                        $xml .= "<list>\n";
                        $xml .= $this->toXml('entry', $val);
                        $xml .= "</list>\n";
                    } else {
                        // Associative key with array value: recurse
                        $xml .= $this->toXml($key, $val);
                    }
                } else {
                    if (is_numeric($key)) {
                        // Numerical key with scalar value: <entry>
                        $xml .= "<entry>{$this->escape($val)}</entry>\n";
                    } else {
                        // Associative key with scalar value
                        $xml .= "<{$key}>{$this->escape($val)}</{$key}>\n";
                    }
                }
            }
            $xml .= "</{$name}>\n";
        } else {
            // Scalar value
            $xml = "<{$name}>{$this->escape($value)}</{$name}>\n";
        }
        return $xml;
    }

    private function escape($value)
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }
}