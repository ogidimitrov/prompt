<?php
namespace Ogi\Prompt;

use Ogi\Prompt\Templates\GeneralPromptTemplate;

require './../vendor/autoload.php';

class ConcretePromptTemplate extends GeneralPromptTemplate
{
    public function setInput($data): void
    {
        $this->input = $data;
    }
}

$prompt = new ConcretePromptTemplate();
$prompt->addContext('Provide recommendations based on user input.');
$prompt->addPurpose('To give tailored advice for improving coding practices.');
$prompt->addGoal('Ensure the recommendations are practical and concise.');
$prompt->addInputDefinition('A brief description of the code the user wants feedback on.');
$prompt->addOutputDefinition('A list of suggestions to improve the user’s code.');
$prompt->addHowtoSteps([
    'Step 1: Analyze the provided code.',
    'Step 2: Identify areas of improvement.',
    'Step 3: Provide actionable feedback with examples.',
]);

echo $prompt->render();