# PHP Error Handler

[](https://opensource.org/licenses/MIT)
[](https://www.php.net/)
[](https://www.google.com/search?q=https://packagist.org/packages/acme/php-error-handler)

A simple, lightweight, and environment-aware custom error and exception handler for PHP applications. It captures all PHP errors and uncaught exceptions, logs them to files, and displays them cleanly during development without exposing sensitive information in production.

## Key Features

  - **Comprehensive Capture**: Catches all levels of PHP errors, including `E_NOTICE`, `E_WARNING`, and fatal errors.
  - **Exception Handling**: Gracefully handles any uncaught `Throwable` (Exceptions and Errors).
  - **Environment-Aware**: Differentiates between `development` and `production` environments.
      - In `development`, it displays detailed, formatted error messages in the browser.
      - In `production`, it hides sensitive details and shows a generic error message.
  - **Robust Logging**: Logs all errors and exceptions to separate, readable files (`error.log` and `exception.log`) regardless of the environment.
  - **Easy Integration**: Install via Composer and enable with a single line of code.
  - **Configurable**: Easily set the application environment and the path for log files.

## Installation

This library is intended to be installed via [Composer](https://getcomposer.org/).

1.  Open your terminal and navigate to your project's root directory.
2.  Run the following command:

<!-- end list -->

```bash
composer require acme/php-error-handler
```

> **Note**: As this is a hypothetical package name, you would replace `acme/php-error-handler` with the actual package name once it's published on Packagist. For local development, you can use a [path repository](https://www.google.com/search?q=https://getcomposer.org/doc/05-repositories.md%23path) in your project's `composer.json`.

## How to Use

The error handler should be registered at the very beginning of your application's lifecycle. This ensures it can catch any errors that happen during script execution. The best place for this is your main entry point, such as `index.php` or a bootstrap file.

### Basic Usage (Development Example)

In your `index.php`:

```php
<?php

// 1. Include Composer's autoloader
// This makes all your Composer packages available.
require_once __DIR__ . '/vendor/autoload.php';

// 2. Import the ErrorHandler class
use IQ\PhpErrorHandler\ErrorHandler;

// 3. Define your application environment (could be loaded from a .env file)
const APP_ENV = 'development';

// 4. Register the error handler
// It is highly recommended to provide an absolute path for the log directory.
ErrorHandler::register(APP_ENV, __DIR__ . '/storage/logs');


// --- The rest of your application code starts here ---

echo "<h1>My Application</h1>";
echo "<p>Let's trigger some errors to test the handler.</p>";

// This notice will be logged.
echo $someUndefinedVariable; 

// This warning will be logged.
include 'a_file_that_does_not_exist.php';

// This exception will be caught, logged, and displayed.
throw new Exception("Something went wrong in the application!");

```

### Production Usage

For a production environment, the only change needed is the environment constant. The handler will automatically adjust its behavior.

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';
use IQ\PhpErrorHandler\ErrorHandler;

// Set the environment to 'production'
const APP_ENV = 'production';

ErrorHandler::register(APP_ENV, __DIR__ . '/storage/logs');


// --- Application Code ---
echo "<h1>Welcome to our website!</h1>";

// If an error or exception occurs now...
// 1. It will be logged to the '/storage/logs' directory.
// 2. The user will see a generic, friendly error message instead of PHP error details.
throw new Exception("A critical database error occurred.");
```

## Configuration

The behavior of the handler is controlled by the parameters passed to the `register` method.

`ErrorHandler::register(string $environment, string $logPath)`

| Parameter       | Type     | Default                           | Description                                                                                                                              |
| --------------- | -------- | --------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------- |
| `$environment`  | `string` | `'production'`                    | Sets the operational mode. Accepts `'development'` for detailed browser output or `'production'` for generic messages.                       |
| `$logPath`      | `string` | `project_root/logs` (estimated) | An **absolute path** to the directory where log files will be saved. If not provided, it attempts to create a `logs` directory in the project root. |

## What to Expect

### In Development Mode

  - **Browser**: Any error or uncaught exception will be displayed in a distinct, styled red box containing the message, file, line number, and a full stack trace for exceptions.
  - **Logs**: All issues are logged to `error.log` and `exception.log` inside your specified log path.

### In Production Mode

  - **Browser**: The user will see a simple, non-technical message like "Application Error". The HTTP response code will be set to 500.
  - **Logs**: All issues are logged with the same level of detail as in development, allowing you to fix problems without exposing them to users.

## Contributing

Contributions are welcome\! For major changes, please open an issue first to discuss what you would like to change. Please ensure to update tests as appropriate.

## License

This project is licensed under the MIT License. See the `LICENSE` file for details.
