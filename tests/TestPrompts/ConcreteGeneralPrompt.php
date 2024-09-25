<?php

namespace Ogi\tests\TestPrompts;

use Ogi\Prompt\Templates\GeneralPromptTemplate;

class ConcreteGeneralPrompt extends GeneralPromptTemplate
{

    public function setInput($data): void
    {
        $this->input = $data;
    }
}