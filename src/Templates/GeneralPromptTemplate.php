<?php

namespace Ogi\Prompt\Templates;

use Ogi\Prompt\Constants;
use Ogi\Prompt\Inputable;
use Ogi\Prompt\Prompt;
use Ogi\Prompt\Renderable;

abstract class GeneralPromptTemplate extends Prompt implements Renderable, Inputable
{
    public string $context = '';
    public string $purpose = '';
    public string $goal = '';
    public array $instructions = [

        // instructions about the input, group-only
        Constants::INPUT => [
            Constants::DEFINITION => '',
            Constants::STRUCTURE => '',
            Constants::VALUE_MEANING => '',
            Constants::POSSIBLE_VALUES => '',
            Constants::IF_INSTRUCTIONS_PER_TYPE => [],
        ],

        // instructions about the output, group-only
        Constants::OUTPUT => [
          Constants::DEFINITION => '',
            Constants::STRUCTURE => '',
            Constants::VALUE_MEANING => '',
            Constants::POSSIBLE_VALUES => '',

            //
            Constants::IF_INSTRUCTIONS_PER_TYPE => [],

            // simple example of valid input for the prompt, can be [] if complicated
            Constants::EXAMPLE_VALID_OUTPUT => '',

            // simple example of unexpected/invalid/unacceptable output, can be [] if complicated
            Constants::EXAMPLE_INVALID_OUTPUT => '',
        ],

        /**
         *
         */
        Constants::HOWTO => [
            Constants::DEFINITION => '',
            Constants::STEPS => [],

            // and explanation of possible corner-cases if applicable for the use case
            Constants::CORNER_CASES => [],

        ],
    ];

    public array $considerations = [];

    public array $struggles = [
//        [Constants::DEFINITION => '', Constants::HELPER => '']
    ];

    /**
     *  The text main input of the prompt
     */
    public string $input = '';


    abstract public function setInput($data): void;

    /**
     *  Helper text for LLM model consumption, like corner-cases but more general
     */
    public function addStruggle(string $definition, string $helper): void
    {
        $this->struggles[] = [Constants::DEFINITION => $definition, Constants::HELPER => $helper];
    }

    /**
     *  Things the LLM  model shall consider working on the prompt
     */
    public function addConsideration(string $consideration): void
    {
        $this->considerations[] = $consideration;
    }

    /**
     *  What is the input
     */
    public function addInputDefinition(string $definition): void
    {
        $this->instructions[Constants::INPUT][Constants::DEFINITION] = $definition;
    }

    /**
     *  What is the structure of the input if applicable (stored in public $input property eventually)
     */
    public function addInputStructure(string $structure): void
    {
        $this->instructions[Constants::INPUT][Constants::STRUCTURE] = $structure;
    }

    /**
     *  What is the meaning of the input, if applicable
     */
    public function addInputValueMeaning(string $valueMeaning): void
    {
        $this->instructions[Constants::INPUT][Constants::VALUE_MEANING] = $valueMeaning;
    }

    /**
     * For a kind of LLM validation purpose, generally shall not be needed due validation shall occur before the api call
     */
    public function addInputPossibleValues(string $possibleValues): void
    {
        $this->instructions[Constants::INPUT][Constants::POSSIBLE_VALUES] = $possibleValues;
    }

    /**
     *  If applicable specific instructions per type of input
     */
    public function addInputIfInstructions(array $instructions): void
    {
        $this->instructions[Constants::INPUT][Constants::IF_INSTRUCTIONS_PER_TYPE] = $instructions;
    }

    /**
     *  What is the output
     */
    public function addOutputDefinition(string $definition): void
    {
        $this->instructions[Constants::OUTPUT][Constants::DEFINITION] = $definition;
    }

    /**
     *  What is the expected/valid structure of the output if applicable
     */
    public function addOutputStructure(string $structure): void
    {
        $this->instructions[Constants::OUTPUT][Constants::STRUCTURE] = $structure;
    }

    /**
     *  If it will be beneficial for the model to know what the output means, literally, if applicable
     */
    public function addOutputValueMeaning(string $valueMeaning): void
    {
        $this->instructions[Constants::OUTPUT][Constants::VALUE_MEANING] = $valueMeaning;
    }

    /**
     *  Define possible restrictions or limitations for the output values, mutators or graceful handling
     */
    public function addOutputPossibleValues(string $possibleValues): void
    {
        $this->instructions[Constants::OUTPUT][Constants::POSSIBLE_VALUES] = $possibleValues;
    }

    /**
     *  If applicable instructions per type/range/intersection of an output value. Can be complicated
     */
    public function addOutputIfInstructions(array $instructions): void
    {
        $this->instructions[Constants::OUTPUT][Constants::IF_INSTRUCTIONS_PER_TYPE] = $instructions;
    }

    public function addOutputExampleValidOutput(string $example): void
    {
        $this->instructions[Constants::OUTPUT][Constants::EXAMPLE_VALID_OUTPUT] = $example;
    }

    public function addOutputExampleInvalidOutput(string $example): void
    {
        $this->instructions[Constants::OUTPUT][Constants::EXAMPLE_INVALID_OUTPUT] = $example;
    }

    /**
     * Simple text explanation how with the given input to create the output
     */
    public function addHowtoDefinition(string $definition): void
    {
        $this->instructions[Constants::HOWTO][Constants::DEFINITION] = $definition;
    }

    /**
     *  Steps if applicable how to reach output considering the input
     */
    public function addHowtoSteps(array $steps): void
    {
        $this->instructions[Constants::HOWTO][Constants::STEPS] = $steps;
    }

    public function addHowtoCornerCases(array $cornerCases): void
    {
        $this->instructions[Constants::HOWTO][Constants::CORNER_CASES] = $cornerCases;
    }

    public function addGoal(string $goal): void
    {
        $this->goal = $goal;
    }

    public function addPurpose(string $purpose): void
    {
        $this->purpose = $purpose;
    }

    public function addContext(string $context): void
    {
        $this->context = $context;
    }

}