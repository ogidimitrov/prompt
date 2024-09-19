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

    private function toXml($name, $value, &$processed = [])
    {

        if (is_object($value)) {
            if (in_array($value, $processed, true)) {
                return "<{$name}></{$name}>\n";
            }
            $processed[] = $value;
        }

        if (is_array($value) || $value instanceof \Traversable) {
            $xml = "<{$name}>\n";
            foreach ($value as $key => $val) {
                $xml .= $this->processValue($key, $val);
            }
            $xml .= "</{$name}>\n";
        } elseif ($value instanceof Prompt) {
            $xml = "<prompt>\n";
            $xml .= $value->render();
            $xml .= "</prompt>\n";
        } elseif (is_object($value) && method_exists($value, 'toArray')) {
            $arrayValue = $value->toArray();
            $xml = $this->toXml($name, $arrayValue);
        } elseif (is_object($value) && method_exists($value, '__toString')) {
            $xml = "<{$name}>{$this->escape((string)$value)}</{$name}>\n";
        } else {
            $xml = "<{$name}>{$this->escape($value)}</{$name}>\n";
        }
        return $xml;
    }

    private function processValue($key, $val)
    {
        if (is_array($val) || $val instanceof \Traversable) {
            if (is_numeric($key)) {
                $xml = "<list>\n";
                $xml .= $this->toXml('entry', $val);
                $xml .= "</list>\n";
            } else {
                $xml = $this->toXml($key, $val);
            }
        } elseif ($val instanceof Prompt) {
            $xml = "<prompt>\n";
            $xml .= $val->render();
            $xml .= "</prompt>\n";
        } elseif (is_numeric($key)) {
            $xml = "<entry>{$this->escape($val)}</entry>\n";
        } else {
            $xml = "<{$key}>{$this->escape($val)}</{$key}>\n";
        }
        return $xml;
    }

    private function escape($value)
    {

        if (is_resource($value)) {
            return '';
        }

        if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                $value = (string)$value;
            } else {
                $value = '';
            }
        }
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }
}
