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

        $expectedXml = "<prompt>\n<resourceProperty></resourceProperty>\n</prompt>\n";
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

        $expectedXml = "<prompt>\n<mixedArray>\n"
            . "<entry>string</entry>\n"
            . "<entry>123</entry>\n"
            . "<entry></entry>\n"
            . "<entry>true</entry>\n"
            . "<entry>Stringable Object</entry>\n"
            . "<entry></entry>\n"
            . "<entry>\n"
            . "<nestedArray>\n"
            . "<key>value</key>\n"
            . "</nestedArray>\n"
            . "</entry>\n"
            . "</mixedArray>\n"
        . "</prompt>\n";

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

        $expectedXml = "<prompt>\n<publicProperty>Public Value</publicProperty>\n</prompt>\n";
        $this->assertEquals($expectedXml, $mockPrompt->render());
    }

    public function testEmptyArrayProperty()
    {
        $mockPrompt = new class extends Prompt {
            public $emptyArray = [];
        };

        $expectedXml = "<prompt>\n<emptyArray>\n</emptyArray>\n</prompt>\n";
        $this->assertEquals($expectedXml, $mockPrompt->render());
    }

    public function testNullProperty()
    {
        $mockPrompt = new class extends Prompt {
            public $nullProperty = null;
        };

        $expectedXml = "<prompt>\n<nullProperty></nullProperty>\n</prompt>\n";
        $this->assertEquals($expectedXml, $mockPrompt->render());
    }

    public function testBooleanProperties()
    {
        $mockPrompt = new class extends Prompt {
            public $trueProperty = true;
            public $falseProperty = false;
        };

        $expectedXml = "<prompt>\n<trueProperty>true</trueProperty>\n<falseProperty>false</falseProperty>\n</prompt>\n";
        $this->assertEquals($expectedXml, $mockPrompt->render());
    }

    public function testNumericProperties()
    {
        $mockPrompt = new class extends Prompt {
            public $integerProperty = 42;
            public $floatProperty = 3.14;
        };

        $expectedXml = "<prompt>\n<integerProperty>42</integerProperty>\n<floatProperty>3.14</floatProperty>\n</prompt>\n";
        $this->assertEquals($expectedXml, $mockPrompt->render());
    }

    public function testSpecialCharactersInValues()
    {
        $mockPrompt = new class extends Prompt {
            public $specialChars = 'Special < & > " \' Characters';
        };

        $expectedXml = "<prompt>\n<specialChars>Special &lt; &amp; &gt; &quot; &apos; Characters</specialChars>\n</prompt>\n";
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

        $expectedXml = "<prompt>\n<nonStringableObject></nonStringableObject>\n</prompt>\n";
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

        $expectedXml = "<prompt>\n<parentProperty>Parent Value</parentProperty>\n<childPrompt>\n<prompt>\n<childProperty>Child Value</childProperty>\n<grandChildPrompt>\n<prompt>\n<grandChildProperty>GrandChild Value</grandChildProperty>\n</prompt>\n</grandChildPrompt>\n</prompt>\n</childPrompt>\n</prompt>\n";
        $this->assertEquals($expectedXml, $parentPrompt->render());
    }
}
