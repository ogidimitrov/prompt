<?php
namespace Ogi\tests;

use PHPUnit\Framework\TestCase;
use Ogi\tests\TestPrompts\ConcreteGeneralPrompt;
use Ogi\Prompt\Constants;

class GeneralPromptTemplateTest extends TestCase
{
    private $prompt;

    protected function setUp(): void
    {
        $this->prompt = new ConcreteGeneralPrompt();
    }

    public function testSetInput()
    {
        $inputData = "This is a test input.";
        $this->prompt->setInput($inputData);
        $this->assertEquals($inputData, $this->prompt->input);
    }

    public function testAddStruggle()
    {
        $definition = "Difficulty understanding context.";
        $helper = "Provide more detailed background information.";
        $this->prompt->addStruggle($definition, $helper);

        $expected = [
            [
                Constants::DEFINITION => $definition,
                Constants::HELPER => $helper
            ]
        ];

        $this->assertEquals($expected, $this->prompt->struggles);
    }

    public function testAddConsideration()
    {
        $consideration = "Ensure clarity in instructions.";
        $this->prompt->addConsideration($consideration);

        $expected = [$consideration];
        $this->assertEquals($expected, $this->prompt->considerations);
    }

    public function testAddInputDefinition()
    {
        $definition = "User's query input.";
        $this->prompt->addInputDefinition($definition);
        $this->assertEquals($definition, $this->prompt->instructions[Constants::INPUT][Constants::DEFINITION]);
    }

    public function testAddInputStructure()
    {
        $structure = "JSON format with key-value pairs.";
        $this->prompt->addInputStructure($structure);
        $this->assertEquals($structure, $this->prompt->instructions[Constants::INPUT][Constants::STRUCTURE]);
    }

    public function testAddInputValueMeaning()
    {
        $valueMeaning = "Represents the user's intent.";
        $this->prompt->addInputValueMeaning($valueMeaning);
        $this->assertEquals($valueMeaning, $this->prompt->instructions[Constants::INPUT][Constants::VALUE_MEANING]);
    }

    public function testAddInputPossibleValues()
    {
        $possibleValues = "Any valid string input.";
        $this->prompt->addInputPossibleValues($possibleValues);
        $this->assertEquals($possibleValues, $this->prompt->instructions[Constants::INPUT][Constants::POSSIBLE_VALUES]);
    }

    public function testAddInputIfInstructions()
    {
        $instructions = [
            "If the input is empty, prompt the user to provide more details."
        ];
        $this->prompt->addInputIfInstructions($instructions);
        $this->assertEquals($instructions, $this->prompt->instructions[Constants::INPUT][Constants::IF_INSTRUCTIONS_PER_TYPE]);
    }

    public function testAddOutputDefinition()
    {
        $definition = "Generated response based on the input.";
        $this->prompt->addOutputDefinition($definition);
        $this->assertEquals($definition, $this->prompt->instructions[Constants::OUTPUT][Constants::DEFINITION]);
    }

    public function testAddOutputStructure()
    {
        $structure = "Structured as a JSON object.";
        $this->prompt->addOutputStructure($structure);
        $this->assertEquals($structure, $this->prompt->instructions[Constants::OUTPUT][Constants::STRUCTURE]);
    }

    public function testAddOutputValueMeaning()
    {
        $valueMeaning = "The content should be relevant and accurate.";
        $this->prompt->addOutputValueMeaning($valueMeaning);
        $this->assertEquals($valueMeaning, $this->prompt->instructions[Constants::OUTPUT][Constants::VALUE_MEANING]);
    }

    public function testAddOutputPossibleValues()
    {
        $possibleValues = "Any valid JSON string.";
        $this->prompt->addOutputPossibleValues($possibleValues);
        $this->assertEquals($possibleValues, $this->prompt->instructions[Constants::OUTPUT][Constants::POSSIBLE_VALUES]);
    }

    public function testAddOutputIfInstructions()
    {
        $instructions = [
            "If the output contains errors, log them for review."
        ];
        $this->prompt->addOutputIfInstructions($instructions);
        $this->assertEquals($instructions, $this->prompt->instructions[Constants::OUTPUT][Constants::IF_INSTRUCTIONS_PER_TYPE]);
    }

    public function testAddOutputExampleValidOutput()
    {
        $example = '{"response": "PHPUnit is a testing framework for PHP."}';
        $this->prompt->addOutputExampleValidOutput($example);
        $this->assertEquals($example, $this->prompt->instructions[Constants::OUTPUT][Constants::EXAMPLE_VALID_OUTPUT]);
    }

    public function testAddOutputExampleInvalidOutput()
    {
        $example = '{"response": "Testing framework is good.", "error": true}';
        $this->prompt->addOutputExampleInvalidOutput($example);
        $this->assertEquals($example, $this->prompt->instructions[Constants::OUTPUT][Constants::EXAMPLE_INVALID_OUTPUT]);
    }

    public function testAddHowtoDefinition()
    {
        $definition = "Transform the input into a coherent response.";
        $this->prompt->addHowtoDefinition($definition);
        $this->assertEquals($definition, $this->prompt->instructions[Constants::HOWTO][Constants::DEFINITION]);
    }

    public function testAddHowtoSteps()
    {
        $steps = [
            "Parse the input data.",
            "Analyze the intent behind the input.",
            "Generate a relevant response based on the analysis."
        ];
        $this->prompt->addHowtoSteps($steps);
        $this->assertEquals($steps, $this->prompt->instructions[Constants::HOWTO][Constants::STEPS]);
    }

    public function testAddHowtoCornerCases()
    {
        $cornerCases = [
            "Handling ambiguous inputs.",
            "Responding to multi-part questions."
        ];
        $this->prompt->addHowtoCornerCases($cornerCases);
        $this->assertEquals($cornerCases, $this->prompt->instructions[Constants::HOWTO][Constants::CORNER_CASES]);
    }

    public function testAddGoal()
    {
        $goal = "Provide accurate and helpful responses to user queries.";
        $this->prompt->addGoal($goal);
        $this->assertEquals($goal, $this->prompt->goal);
    }

    public function testAddPurpose()
    {
        $purpose = "Enhance user interaction through intelligent responses.";
        $this->prompt->addPurpose($purpose);
        $this->assertEquals($purpose, $this->prompt->purpose);
    }

    public function testAddContext()
    {
        $context = "The user is seeking information about PHP testing.";
        $this->prompt->addContext($context);
        $this->assertEquals($context, $this->prompt->context);
    }

    public function testRender()
    {
        $this->prompt->addContext("Testing Context");
        $this->prompt->addPurpose("Provide proper test scenarios");

        $expectedXml = "<prompt>\n" .
            "<context>Testing Context</context>\n" .
            "<purpose>Provide proper test scenarios</purpose>\n" .
            "<goal></goal>\n" .
            "<instructions>\n" .
            "<input>\n" .
            "<definition></definition>\n" .
            "<structure></structure>\n" .
            "<value-meaning></value-meaning>\n" .
            "<possible-values></possible-values>\n" .
            "<if-instructions-per-type>\n" .
            "</if-instructions-per-type>\n" .
            "</input>\n" .
            "<output>\n" .
            "<definition></definition>\n" .
            "<structure></structure>\n" .
            "<value-meaning></value-meaning>\n" .
            "<possible-values></possible-values>\n" .
            "<if-instructions-per-type>\n" .
            "</if-instructions-per-type>\n" .
            "<example-valid-output></example-valid-output>\n" .
            "<example-invalid-output></example-invalid-output>\n" .
            "</output>\n" .
            "<howto>\n" .
            "<definition></definition>\n" .
            "<steps>\n" .
            "</steps>\n" .
            "<corner-cases>\n" .
            "</corner-cases>\n" .
            "</howto>\n" .
            "</instructions>\n" .
            "<considerations>\n" .
            "</considerations>\n" .
            "<struggles>\n" .
            "</struggles>\n" .
            "<input></input>\n" .
            "</prompt>\n";

        $this->assertEquals($expectedXml, $this->prompt->render());
    }
}