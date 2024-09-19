# Ogi Prompt

A PHP package that transforms class properties into XML-formatted prompts, ready for Large Language Model (LLM) API calls. It recursively handles arrays, collections, nested arrays, and nested `Prompt` instances, converting them into XML with appropriate tags.

---

## Table of Contents

- [Installation](#installation)
- [Features](#features)
- [Usage](#usage)
  - [Basic Example](#basic-example)
  - [Handling Arrays](#handling-arrays)
  - [Using Collections](#using-collections)
  - [Nested Prompt Instances](#nested-prompt-instances)
  - [Custom Objects with `toArray()` or `__toString()`](#custom-objects-with-toarray-or-__tostring__)
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
- **Recursive Processing**: Recursively processes nested arrays and collections to any depth.
- **Easy Integration**: Extend the `Prompt` class and define your data; the `render()` method handles the rest.

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
    public $title = 'Ogi Prompt Basic Example';
    public $message = 'Hello, this is a basic example.';
}

$prompt = new MyPrompt();
echo $prompt->render();
```

**Output:**

```xml
<title>Ogi Prompt Basic Example</title>
<message>Hello, this is a basic example.</message>
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
<questions>
<entry>What is your name?</entry>
<entry>How old are you?</entry>
<entry>What is your favorite color?</entry>
</questions>
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
<items>
<entry>First Item</entry>
<entry>Second Item</entry>
<list>
<entry>Nested Item 1</entry>
<entry>Nested Item 2</entry>
</list>
<entry>Third Item</entry>
</items>
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
<title>Main Prompt</title>
<prompt>
<message>This is a sub-prompt.</message>
<details>
<detail1>Detail One</detail1>
<detail2>Detail Two</detail2>
</details>
</prompt>
```

---

### Custom Objects with `toArray()` or `__toString()`

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
    }
}

$prompt = new MyPrompt();
echo $prompt->render();
```

**Output:**

```xml
<customData>
<keyA>Value A</keyA>
<keyB>Value B</keyB>
</customData>
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
<stringable>I can be converted to a string!</stringable>
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

- **PHP Version**: Ensure your PHP version is 7.2 or higher.
- **Dependencies**: If you use collections from frameworks like Laravel, make sure to include the necessary packages (e.g., `illuminate/support`).
- **Error Handling**: The package gracefully handles objects without `__toString()` or `toArray()` by ignoring them or representing them as empty strings.
- **Testing**: It's recommended to write tests using PHPUnit to ensure the reliability of your implementation.
- **Customization**: Feel free to extend the `Prompt` class and override methods for custom behavior.

---

Let me know if you have any questions or need further assistance!