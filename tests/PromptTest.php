<?php

namespace Ogi\tests;

use PHPUnit\Framework\TestCase;
use Ogi\Prompt\Prompt;

class PromptTest extends TestCase
{
    public function testRenderWithScalarProperties()
    {
        // Create a mock class that extends Prompt
        $mockPrompt = new class extends Prompt {
            public $title = 'Test Title';
            public $description = 'Test Description';
        };

        $expectedXml = "<title>Test Title</title>\n<description>Test Description</description>\n";
        $this->assertEquals($expectedXml, $mockPrompt->render());
    }

    public function testRenderWithArrayProperties()
    {
        $mockPrompt = new class extends Prompt {
            public $items = ['Item 1', 'Item 2', 'Item 3'];
        };

        $expectedXml = "<items>\n<entry>Item 1</entry>\n<entry>Item 2</entry>\n<entry>Item 3</entry>\n</items>\n";
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

        $expectedXml = "<data>\n<numbers>\n<entry>1</entry>\n<entry>2</entry>\n<entry>3</entry>\n</numbers>\n<letters>\n<entry>a</entry>\n<entry>b</entry>\n<entry>c</entry>\n</letters>\n</data>\n";
        $this->assertEquals($expectedXml, $mockPrompt->render());
    }
}