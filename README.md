# Ogi Prompt

A PHP package that transforms class properties into XML-formatted prompts, ready for Large Language Model (LLM) API calls. It recursively handles arrays and nested arrays, converting them into XML with `<entry>` and `<list>` tags.

---

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
    - [Basic Example](#basic-example)
- [Features](#features)
- [Contributing](#contributing)
- [License](#license)

---

## Installation

Install the package via Composer:

```bash
composer require ogi/prompt
```

---

## Usage

### Basic Example

Create a PHP class that extends the `Prompt` class provided by the package. Define your data using public properties, including arrays and nested arrays as needed.

```php
<?php

require 'vendor/autoload.php';

use Ogi\Prompt\Prompt;

class MyPrompt extends Prompt
{
    public $title = 'Ogi Prompt Example';
    public $questions = [
        'What is the capital of France?',
        'Who wrote "Hamlet"?',
        'Compute the integral of x^2.',
    ];
    public $nestedData = [
        'math' => [
            'algebra' => [
                'Solve for x: 2x + 3 = 7',
                'Factor the expression x^2 - 5x + 6',
            ],
            'calculus' => [
                'Differentiate y = x^3',
                'Integrate y = 1/x',
            ],
        ],
        'literature' => [
            'Identify the protagonist in "1984".',
            'Explain the theme of "The Great Gatsby".',
        ],
    ];
}

$prompt = new MyPrompt();
echo $prompt->render();
```

**Output:**

```xml
<title>Ogi Prompt Example</title>
<questions>
<entry>What is the capital of France?</entry>
<entry>Who wrote "Hamlet"?</entry>
<entry>Compute the integral of x^2.</entry>
</questions>
<nestedData>
<math>
<algebra>
<entry>Solve for x: 2x + 3 = 7</entry>
<entry>Factor the expression x^2 - 5x + 6</entry>
</algebra>
<calculus>
<entry>Differentiate y = x^3</entry>
<entry>Integrate y = 1/x</entry>
</calculus>
</math>
<literature>
<entry>Identify the protagonist in "1984".</entry>
<entry>Explain the theme of "The Great Gatsby".</entry>
</literature>
</nestedData>
```

---

## Features

- **Automatic XML Conversion**: Transforms public class properties into XML elements.
- **Array Handling**: Supports arrays and nested arrays, using `<entry>` and `<list>` tags where appropriate.
- **Recursive Processing**: Recursively processes nested arrays to any depth.
- **Easy Integration**: Extend the `Prompt` class and define your data; the `render()` method handles the rest.

---

## Contributing

Contributions are welcome! Please submit a pull request or open an issue to discuss your ideas.

---

## License

This project is licensed under the [MIT License](LICENSE).

---

**Note**: Ensure your PHP version is 7.2 or higher. The package uses PSR-4 autoloading for seamless integration.