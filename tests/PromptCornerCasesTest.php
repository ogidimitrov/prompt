<?php

namespace Ogi\tests;

use PHPUnit\Framework\TestCase;
use Ogi\Prompt\Prompt;

class PromptCornerCasesTest extends TestCase
{

    public function testResourceProperty()
    {
        $resource = fopen('php://temp', 'r');
        $mockPrompt = new class($resource) extends Prompt {
            public $resourceProperty;

            public function __construct($resource)
            {
                $this->resourceProperty = $resource;
            }
        };

        $expectedXml = "<resourceProperty></resourceProperty>\n";
        $this->assertEquals($expectedXml, $mockPrompt->render());

        fclose($resource);
    }

    public function testMixedTypeArray()
    {
        $resource = fopen('php://temp', 'r');
        $mockPrompt = new class($resource) extends Prompt {
            public $mixedArray;

            public function __construct($resource)
            {
                $this->mixedArray = [
                    'string',
                    123,
                    null,
                    true,
                    new class {
                        public function __toString()
                        {
                            return 'Stringable Object';
                        }
                    },
                    $resource,
                    ['nestedArray' => ['key' => 'value']],
                ];
            }
        };

        $expectedXml = "<mixedArray>\n"
            . "<entry>string</entry>\n"
            . "<entry>123</entry>\n"
            . "<entry></entry>\n"
            . "<entry>1</entry>\n"
            . "<entry>Stringable Object</entry>\n"
            . "<entry></entry>\n"
            . "<list>\n"
            . "<entry>\n"
            . "<nestedArray>\n"
            . "<key>value</key>\n"
            . "</nestedArray>\n"
            . "</entry>\n"
            . "</list>\n"
            . "</mixedArray>\n";

        $this->assertEquals($expectedXml, $mockPrompt->render());

        fclose($resource);
    }

    public function testPrivateAndProtectedPropertiesAreExcluded()
    {
        $mockPrompt = new class extends Prompt {
            public $publicProperty = 'Public Value';
            protected $protectedProperty = 'Protected Value';
            private $privateProperty = 'Private Value';
        };

        $expectedXml = "<publicProperty>Public Value</publicProperty>\n";
        $this->assertEquals($expectedXml, $mockPrompt->render());
    }

    public function testEmptyArrayProperty()
    {
        $mockPrompt = new class extends Prompt {
            public $emptyArray = [];
        };

        $expectedXml = "<emptyArray>\n</emptyArray>\n";
        $this->assertEquals($expectedXml, $mockPrompt->render());
    }

    public function testNullProperty()
    {
        $mockPrompt = new class extends Prompt {
            public $nullProperty = null;
        };

        $expectedXml = "<nullProperty></nullProperty>\n";
        $this->assertEquals($expectedXml, $mockPrompt->render());
    }

    public function testBooleanProperties()
    {
        $mockPrompt = new class extends Prompt {
            public $trueProperty = true;
            public $falseProperty = false;
        };

        $expectedXml = "<trueProperty>1</trueProperty>\n<falseProperty></falseProperty>\n";
        $this->assertEquals($expectedXml, $mockPrompt->render());
    }

    public function testNumericProperties()
    {
        $mockPrompt = new class extends Prompt {
            public $integerProperty = 42;
            public $floatProperty = 3.14;
        };

        $expectedXml = "<integerProperty>42</integerProperty>\n<floatProperty>3.14</floatProperty>\n";
        $this->assertEquals($expectedXml, $mockPrompt->render());
    }

    public function testSpecialCharactersInValues()
    {
        $mockPrompt = new class extends Prompt {
            public $specialChars = 'Special < & > " \' Characters';
        };

        $expectedXml = "<specialChars>Special &lt; &amp; &gt; &quot; &apos; Characters</specialChars>\n";
        $this->assertEquals($expectedXml, $mockPrompt->render());
    }

    public function testObjectWithoutToStringOrToArray()
    {
        $mockPrompt = new class extends Prompt {
            public $nonStringableObject;

            public function __construct()
            {
                $this->nonStringableObject = new class {
                    public $data = 'Some Data';
                };
            }
        };

        $expectedXml = "<nonStringableObject></nonStringableObject>\n";
        $this->assertEquals($expectedXml, $mockPrompt->render());
    }

    public function testRecursivePromptInstances()
    {
        $grandChildPrompt = new class extends Prompt {
            public $grandChildProperty = 'GrandChild Value';
        };

        $childPrompt = new class($grandChildPrompt) extends Prompt {
            public $childProperty = 'Child Value';
            public $grandChildPrompt;

            public function __construct($grandChildPrompt)
            {
                $this->grandChildPrompt = $grandChildPrompt;
            }
        };

        $parentPrompt = new class($childPrompt) extends Prompt {
            public $parentProperty = 'Parent Value';
            public $childPrompt;

            public function __construct($childPrompt)
            {
                $this->childPrompt = $childPrompt;
            }
        };

        $expectedXml = "<parentProperty>Parent Value</parentProperty>\n<prompt>\n<childProperty>Child Value</childProperty>\n<prompt>\n<grandChildProperty>GrandChild Value</grandChildProperty>\n</prompt>\n</prompt>\n";
        $this->assertEquals($expectedXml, $parentPrompt->render());
    }
}
