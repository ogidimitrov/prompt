<?php

namespace Ogi\tests\TestPrompts;

use Ogi\Prompt\Prompt;

class MockPrompt extends Prompt
{
    public $nested;

    public function __construct($nested)
    {
        $this->nested = $nested;
    }
}
