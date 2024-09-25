<?php

namespace Ogi\tests;

use PHPUnit\Framework\TestCase;
use Ogi\Prompt\Prompt;
use Ogi\tests\TestPrompts\NestedPrompt;
use Ogi\tests\TestPrompts\MockPrompt;

class PromptTest extends TestCase
{
    public function testRenderWithScalarProperties()
    {
        // Create a mock class that extends Prompt
        $mockPrompt = new class extends Prompt {
            public $title = 'Test Title';
            public $description = 'Test Description';
        };

        $expectedXml = "<prompt>\n" .
            "<title>Test Title</title>\n" .
            "<description>Test Description</description>\n" .
            "</prompt>\n";
        $this->assertEquals($expectedXml, $mockPrompt->render());
    }

    public function testRenderWithArrayProperties()
    {
        $mockPrompt = new class extends Prompt {
            public $items = ['Item 1', 'Item 2', 'Item 3'];
        };

        $expectedXml = "<prompt>\n" .
            "<items>\n" .
            "<entry>Item 1</entry>\n" .
            "<entry>Item 2</entry>\n" .
            "<entry>Item 3</entry>\n" .
            "</items>\n" .
            "</prompt>\n";
        $this->assertEquals($expectedXml, $mockPrompt->render());
    }

    public function testRenderWithNestedArrays()
    {
        $mockPrompt = new class extends Prompt {
            public $data = [
                'numbers' => [1, 2, 3],
                'letters' => ['a', 'b', 'c'],
            ];
        };

        $expectedXml = "<prompt>\n" .
            "<data>\n" .
            "<numbers>\n" .
            "<entry>1</entry>\n" .
            "<entry>2</entry>\n" .
            "<entry>3</entry>\n" .
            "</numbers>\n" .
            "<letters>\n" .
            "<entry>a</entry>\n" .
            "<entry>b</entry>\n" .
            "<entry>c</entry>\n" .
            "</letters>\n" .
            "</data>\n" .
            "</prompt>\n";
        $this->assertEquals($expectedXml, $mockPrompt->render());
    }

    public function testRenderWithCircularReference()
    {
        $mockPrompt = new class extends Prompt {
            public $self;

            public function __construct()
            {
                $this->self = $this;
            }
        };

        $expectedXml = "<prompt>\n" .
            "<self>\n" .
            "<self></self>\n" .
            "</self>\n" .
            "</prompt>\n";
        $this->assertEquals($expectedXml, $mockPrompt->render());
    }

    public function testRenderWithNestedPromptObjects()
    {
        // Create an instance of NestedPrompt
        $nestedPrompt = new NestedPrompt();

        $mockPrompt = new MockPrompt($nestedPrompt);
        // Define the expected XML output
        $expectedXml = "<prompt>\n" .
            "<nested>\n" .
            "<prompt>\n" .
            "<name>Nested Prompt</name>\n" .
            "</prompt>\n" .
            "</nested>\n" .
            "</prompt>\n";

        // Assert that the actual rendered XML matches the expected XML
        $this->assertEquals($expectedXml, $mockPrompt->render());
    }

}
