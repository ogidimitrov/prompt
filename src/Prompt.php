<?php

namespace Ogi\Prompt;

class Prompt
{
    public function render($processed = [])
    {
        // Get the current object hash to track processing
        $objectHash = spl_object_hash($this);

        // If this object has already been processed, return an empty element to prevent recursion
        if (isset($processed[$objectHash])) {
            return "<self></self>\n";
        }

        // Mark this object as processed
        $processed[$objectHash] = true;

        $xmlContent = '';

        $reflection = new \ReflectionObject($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            $name = $property->getName();
            $value = $property->getValue($this);
            $xmlContent .= $this->toXml($name, $value, $processed);
        }

        return "<prompt>\n" . $xmlContent . "</prompt>\n";
    }

    private function toXml($name, $value, &$processed)
    {
        $name =  is_numeric($name) ? 'entry' : $name;

        if (is_bool($value)) {
            $stringValue =  ($value === true) ? 'true' : 'false';
            $xml = $this->toXml($name, $stringValue, $processed);
        }
        elseif (is_array($value)) {
            $xml = "<{$name}>\n";
            foreach ($value as $key => $val) {
                $xml .= $this->toXml($key, $val, $processed);
            }
            $xml .= "</{$name}>\n";
        }
        elseif ($value instanceof Prompt) {
            $xml = "<{$name}>\n" . $value->render($processed) . "</{$name}>\n";
        }
        elseif (is_object($value) && (!method_exists($value, '__toString') && !method_exists($value, 'toArray'))) {
            $xml = "<{$name}></{$name}>\n";
        }
        elseif (is_object($value) && method_exists($value, '__toString')) {
            $xml = "<{$name}>" . $value . "</{$name}>\n";
        }
        elseif (is_object($value) && method_exists($value, 'toArray')) {
            $xml = "<{$name}>\n";
            foreach ($value->toArray() as $key => $val) {
                $xml .= $this->toXml($key, $val, $processed);
            }
            $xml .= "</{$name}>\n";
        }
        elseif (is_resource($value)) {
            $xml = "<{$name}></{$name}>\n";
        }
        else {
            $xml = "<{$name}>{$this->escape($value)}</{$name}>\n";
        }
        return $xml;
    }

    private function escape($value)
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }
}
