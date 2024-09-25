[![CI](https://github.com/ogidimitrov/prompt/actions/workflows/tests.yml/badge.svg)](https://github.com/ogidimitrov/prompt/actions)


# Prompt

A PHP package that transforms class properties into XML-formatted prompts, ready for Large Language Model (LLM) API calls. It recursively handles arrays, collections, nested arrays, and nested `Prompt` instances, converting them into XML with appropriate tags.

---

## Why XML Structured Prompts?

Structured prompts are essential when interacting with Large Language Models (LLMs) to ensure consistency, clarity, and optimal performance. XML provides a standardized way to structure data, making it an excellent choice for formatting prompts:

- **Consistency Across Models**: XML's hierarchical structure allows for consistent formatting of prompts, which can be beneficial when working with different models or APIs.
- **Improved Parsing**: XML is a widely accepted format that can be easily parsed and validated, reducing the likelihood of errors in prompt interpretation.
- **Flexibility**: XML's ability to represent complex nested data makes it suitable for constructing detailed and intricate prompts.
- **Readability**: Structured XML prompts are more readable and maintainable, aiding in debugging and prompt optimization.

By using XML-formatted prompts, developers can provide clear and unambiguous instructions to LLMs, potentially enhancing the models' understanding and response accuracy.

---

## Table of Contents

- [Installation](#installation)
- [Features](#features)
- [Usage](#usage)
  - [Basic Example](#basic-example)
  - [Handling Arrays](#handling-arrays)
  - [Using Collections](#using-collections)
  - [Nested Prompt Instances](#nested-prompt-instances)
  - [Using the GeneralPromptTemplate](#using-the-generalprompttemplate)
  - [Custom Objects with `toArray()` or `__toString__`](#custom-objects-with-toarray-or-__tostring__)
  - [Handling Special Cases](#handling-special-cases)
    - [Private and Protected Properties](#private-and-protected-properties)
    - [Null and Empty Values](#null-and-empty-values)
    - [Boolean Values](#boolean-values)
    - [Numeric Values](#numeric-values)
    - [Special Characters](#special-characters)
    - [Objects Without `toArray()` or `__toString__`](#objects-without-toarray-or-__tostring__)
    - [Circular References](#circular-references)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)
- [Additional Notes](#additional-notes)

---

## Installation

Install the package via Composer:

```bash
composer require ogi/prompt
```

---

## Features

- **Automatic XML Conversion**: Transforms public class properties into XML elements.
- **Array and Collection Handling**: Supports arrays, collections, and nested arrays, using `<entry>` and `<list>` tags where appropriate.
- **Nested Prompt Instances**: Allows properties to be instances of `Prompt`, rendering them recursively within `<prompt>` tags.
- **Custom Object Support**: Handles objects implementing `toArray()` or `__toString()`.
- **Edge Case Handling**: Manages special cases like private/protected properties, null values, booleans, numerics, special characters, and circular references.
- **Recursive Processing**: Recursively processes nested arrays and collections to any depth.
- **Easy Integration**: Extend the `Prompt` class and define your data; the `render()` method handles the rest.
- **Templates**: Extend the `GeneralPromptTemplate` class and leverage its setters to populate a versatile, general-use template prompt for your data.

---

## Usage

### Basic Example

Create a PHP class that extends the `Prompt` class provided by the package. Define your data using public properties.

```php
<?php

require 'vendor/autoload.php';

use Ogi\Prompt\Prompt;

class MyPrompt extends Prompt
{
    public $purpose = 'Who are the leaders of England, France and Russia when World War 2 started';
    public $instructions = [
        'For definition of leader consider titles - king, prime-minister, president',
        'Present the output in a markdown table with columns leader title,name, country'
    ];
}

$prompt = new MyPrompt();
echo $prompt->render();
```

**Output:**

```xml
<prompt>
  <purpose>Who are the leaders of England, France and Russia when World War 2 started</purpose>
  <instructions>
    <entry>For definition of leader consider titles - king, prime-minister, president</entry>
    <entry>Present the output in a markdown table with columns leader title,name, country</entry>
  </instructions>
</prompt>
```

---

### Handling Arrays

The package automatically handles arrays and converts them into XML using `<entry>` and `<list>` tags.

```php
<?php

class MyPrompt extends Prompt
{
    public $questions = [
        'What is your name?',
        'How old are you?',
        'What is your favorite color?',
    ];
}

$prompt = new MyPrompt();
echo $prompt->render();
```

**Output:**

```xml
<prompt>
  <questions>
    <entry>What is your name?</entry>
    <entry>How old are you?</entry>
    <entry>What is your favorite color?</entry>
  </questions>
</prompt>
```

---

### Using Collections

The package supports any object that implements the `Traversable` interface, such as PHP's `ArrayObject` or collections from frameworks like Laravel's `Illuminate\Support\Collection`.

```php
<?php

use Illuminate\Support\Collection;

class MyPrompt extends Prompt
{
    public $items;

    public function __construct()
    {
        $this->items = new Collection([
            'First Item',
            'Second Item',
            new Collection(['Nested Item 1', 'Nested Item 2']),
            'Third Item',
        ]);
    }
}

$prompt = new MyPrompt();
echo $prompt->render();
```

**Output:**

```xml
<prompt>
  <items>
    <entry>First Item</entry>
    <entry>Second Item</entry>
    <list>
      <entry>Nested Item 1</entry>
      <entry>Nested Item 2</entry>
    </list>
    <entry>Third Item</entry>
  </items>
</prompt>
```

---

### Nested Prompt Instances

You can have properties that are instances of the `Prompt` class. These will be rendered recursively within `<prompt>` tags.

```php
<?php

class SubPrompt extends Prompt
{
    public $message = 'This is a sub-prompt.';
    public $details = [
        'detail1' => 'Detail One',
        'detail2' => 'Detail Two',
    ];
}

class MyPrompt extends Prompt
{
    public $title = 'Main Prompt';
    public $subPrompt;

    public function __construct()
    {
        $this->subPrompt = new SubPrompt();
    }
}

$prompt = new MyPrompt();
echo $prompt->render();
```

**Output:**

```xml
<prompt>
  <title>Main Prompt</title>
  <subPrompt>
    <prompt>
      <message>This is a sub-prompt.</message>
      <details>
        <detail1>Detail One</detail1>
        <detail2>Detail Two</detail2>
      </details>
    </prompt>
  </subPrompt>
</prompt>
```

---

### Using the GeneralPromptTemplate

The `GeneralPromptTemplate` is an abstract class that allows you to define more structured prompts with context, purpose, goals, and instructions for input and output. You can extend this class and set properties to generate XML prompts.

```php
<?php

require 'vendor/autoload.php';

use Ogi\Prompt\Templates\GeneralPromptTemplate;

class MyPrompt extends GeneralPromptTemplate
{
    public function setInput($data): void
    {
        $this->input = $data;
    }
}

$prompt = new MyPrompt();
$prompt->addContext('Provide recommendations based on user input.');
$prompt->addPurpose('To give tailored advice for improving coding practices.');
$prompt->addGoal('Ensure the recommendations are practical and concise.');
$prompt->addInputDefinition('A brief description of the code the user wants feedback on.');
$prompt->addOutputDefinition('A list of suggestions to improve the user’s code.');
$prompt->addHowtoSteps([
    'Analyze the provided code.',
    'Identify areas of improvement.',
    'Provide actionable feedback with examples.',
]);

echo $prompt->render();

```

**Output:**

```xml
<prompt>
  <context>Provide recommendations based on user input.</context>
  <purpose>To give tailored advice for improving coding practices.</purpose>
  <goal>Ensure the recommendations are practical and concise.</goal>
  <instructions>
    <input>
      <definition>A brief description of the code the user wants feedback on.</definition>
      <structure></structure>
      <value-meaning></value-meaning>
      <possible-values></possible-values>
      <if-instructions-per-type>
      </if-instructions-per-type>
    </input>
    <output>
      <definition>A list of suggestions to improve the user’s code.</definition>
      <structure></structure>
      <value-meaning></value-meaning>
      <possible-values></possible-values>
      <if-instructions-per-type>
      </if-instructions-per-type>
      <example-valid-output></example-valid-output>
      <example-invalid-output></example-invalid-output>
    </output>
    <howto>
      <definition></definition>
      <steps>
        <entry>Analyze the provided code.</entry>
        <entry>Identify areas of improvement.</entry>
        <entry>Provide actionable feedback with examples.</entry>
      </steps>
      <corner-cases>
      </corner-cases>
    </howto>
  </instructions>
  <considerations>
  </considerations>
  <struggles>
  </struggles>
  <input></input>
</prompt>
```

---


### Custom Objects with `toArray()` or `__toString__`

The package can handle custom objects that implement `toArray()` or `__toString()`.

#### **Objects with `toArray()`**

```php
<?php

class CustomObject
{
    public function toArray()
    {
        return [
            'keyA' => 'Value A',
            'keyB' => 'Value B',
        ];
    }
}

class MyPrompt extends Prompt
{
    public $customData;

    public function __construct()
    {
        $this->customData = new CustomObject();
        $this->customArray = [1,2, new CustomObject()];
    }
}

$prompt = new MyPrompt();
echo $prompt->render();
```

**Output:**

```xml
<prompt>
  <customData>
    <keyA>Value A</keyA>
    <keyB>Value B</keyB>
  </customData>
  <customArray>
    <entry>1</entry>
    <entry>2</entry>
    <entry>
      <keyA>Value A</keyA>
      <keyB>Value B</keyB>
    </keyB>
  </customArray>
</prompt>
```

#### **Objects with `__toString()`**

```php
<?php

class StringableObject
{
    public function __toString()
    {
        return 'I can be converted to a string!';
    }
}

class MyPrompt extends Prompt
{
    public $stringable;

    public function __construct()
    {
        $this->stringable = new StringableObject();
    }
}

$prompt = new MyPrompt();
echo $prompt->render();
```

**Output:**

```xml
<prompt>
  <stringable>I can be converted to a string!</stringable>
</prompt>
```

---

### Handling Special Cases

#### Private and Protected Properties

Only **public** properties are included in the XML output. Private and protected properties are excluded.

```php
<?php

class MyPrompt extends Prompt
{
    public $publicProperty = 'Public Value';
    protected $protectedProperty = 'Protected Value';
    private $privateProperty = 'Private Value';
}

$prompt = new MyPrompt();
echo $prompt->render();
```

**Output:**

```xml
<prompt>
  <publicProperty>Public Value</publicProperty>
</prompt>
```

#### Null and Empty Values

Properties with `null` values or empty arrays are handled gracefully.

```php
<?php

class MyPrompt extends Prompt
{
    public $nullProperty = null;
    public $emptyArray = [];
}

$prompt = new MyPrompt();
echo $prompt->render();
```

**Output:**

```xml
<prompt>
  <nullProperty></nullProperty>
  <emptyArray>
  </emptyArray>
</prompt>
```

#### Boolean Values

Boolean values are converted as follows:

- `true` becomes `1`
- `false` becomes an empty string

```php
<?php

class MyPrompt extends Prompt
{
    public $trueProperty = true;
    public $falseProperty = false;
}

$prompt = new MyPrompt();
echo $prompt->render();
```

**Output:**

```xml
<prompt>
  <trueProperty>true</trueProperty>
  <falseProperty>false</falseProperty>
</prompt>
```

#### Numeric Values

Numeric values are converted to strings and included in the output.

```php
<?php

class MyPrompt extends Prompt
{
    public $integerProperty = 42;
    public $floatProperty = 3.14;
}

$prompt = new MyPrompt();
echo $prompt->render();
```

**Output:**

```xml
<prompt>
  <integerProperty>42</integerProperty>
  <floatProperty>3.14</floatProperty>
</prompt>
```

#### Special Characters

Special characters are properly escaped to produce valid XML.

```php
<?php

class MyPrompt extends Prompt
{
    public $specialChars = 'Special < & > " \' Characters';
}

$prompt = new MyPrompt();
echo $prompt->render();
```

**Output:**

```xml
<prompt>
  <specialChars>Special &lt; &amp; &gt; &quot; &apos; Characters</specialChars>
</prompt>
```

#### Objects Without `toArray()` or `__toString__`

Objects that cannot be converted to a string or array are represented as empty tags.

```php
<?php

class NonStringableObject
{
    public $data = 'Some Data';
}

class MyPrompt extends Prompt
{
    public $nonStringableObject;

    public function __construct()
    {
        $this->nonStringableObject = new NonStringableObject();
    }
}

$prompt = new MyPrompt();
echo $prompt->render();
```

**Output:**

```xml
<prompt>
  <nonStringableObject></nonStringableObject>
</prompt>
```

#### Circular References

The package handles circular references gracefully to prevent infinite recursion.

```php
<?php

class MyPrompt extends Prompt
{
    public $me;

    public function __construct()
    {
        $this->me = $this;
    }
}

$prompt = new MyPrompt();
echo $prompt->render();
```

**Output:**

```xml
<prompt>
  <me>
    <self></self>
  </me>
</prompt>
```

---

## Testing

The package includes a comprehensive test suite using PHPUnit to ensure reliability and correctness. Tests cover:

- Inclusion of public properties and exclusion of private/protected ones.
- Handling of null values, empty arrays, booleans, numerics, and special characters.
- Processing of objects with and without `toArray()` or `__toString__`.
- Recursive rendering of nested `Prompt` instances.
- Edge cases like circular references and mixed-type arrays.

### Running Tests

To run the tests, execute:

```bash
./vendor/bin/phpunit
```

Or if you have added a Composer script:

```bash
composer test
```

---

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository on GitHub.
2. Create a new branch for your feature or bugfix.
3. Write tests for your changes.
4. Ensure all tests pass.
5. Submit a pull request with a detailed description of your changes.

---

## License

This project is licensed under the [MIT License](LICENSE).

---

## Additional Notes

- **PHP Version**: Ensure your PHP version is 7.4 or higher.
- **Dependencies**: If you use collections from frameworks like Laravel, make sure to include the necessary packages (e.g., `illuminate/support`).
- **Circular References**: Circular references are detected to prevent infinite loops during rendering.

---

**Need Help?**

If you have any questions or need assistance, please open an issue on the [GitHub repository](https://github.com/ogidimitrov/prompt).

---

**Happy Coding!**