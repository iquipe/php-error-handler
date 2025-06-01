<?php

// 1. Include Composer's autoloader
require_once 'vendor/autoload.php';

// 2. Import the class with a 'use' statement
use IQ\PhpErrorHandler\ErrorHandler;

// 3. Define the environment (this could come from a .env file)
const APP_ENV = 'development'; // or 'production'

// 4. Register the handler
// It's best practice to provide an absolute path for logs.
ErrorHandler::register(APP_ENV, __DIR__ . '/logs');

// --- Your Application Code ---
echo "<h1>Testing the Composer Error Handler Library</h1>";

// Trigger a warning (will be logged)
$file = file_get_contents('non_existent_file.php');

// Throw an exception (will be logged and displayed)
throw new Exception("This is a test of the library's exception handler.");