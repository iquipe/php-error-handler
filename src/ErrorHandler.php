<?php

namespace IQ\PhpErrorHandler;

use Throwable; // Use Throwable to catch both Errors and Exceptions in PHP 7+

class ErrorHandler
{
    /**
     * @var string The current environment ('development' or 'production').
     */
    private static string $environment = 'production';

    /**
     * @var string The absolute path to the log directory.
     */
    private static string $logPath;

    /**
     * Registers the custom error and exception handlers.
     *
     * @param string $environment The application environment ('development' or 'production').
     * @param string $logPath The absolute path to the directory where logs should be stored.
     */
    public static function register(string $environment = 'production', string $logPath = '')
    {
        self::$environment = $environment;

        // Default to a 'logs' directory in the project root if not specified
        if (empty($logPath)) {
            // A common pattern is to assume a project root one level above vendor/acme/php-error-handler
            self::$logPath = dirname(__DIR__, 4) . '/logs'; 
        } else {
            self::$logPath = $logPath;
        }
        
        // Error reporting setup
        error_reporting(E_ALL);
        ini_set('display_errors', ($environment === 'development') ? '1' : '0');
        ini_set('display_startup_errors', ($environment === 'development') ? '1' : '0');

        // Set the handlers
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);

        // Ensure the log directory exists
        if (!is_dir(self::$logPath)) {
            mkdir(self::$logPath, 0777, true);
        }
    }

    /**
     * Custom error handler.
     */
    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        $errorType = [
             E_ERROR              => 'Fatal Error',
             E_WARNING            => 'Warning',
             E_PARSE              => 'Parse Error',
             E_NOTICE             => 'Notice',
             // ... (include all other error types from your original code)
             E_STRICT             => 'Strict Standards',
             E_RECOVERABLE_ERROR  => 'Recoverable Error',
             E_DEPRECATED         => 'Deprecated',
        ];

        $errorMessage = $errorType[$errno] ?? 'Unknown Error';
        
        $logMessage = sprintf(
            "[%s] %s: %s in %s on line %d\n",
            date('Y-m-d H:i:s'),
            $errorMessage,
            $errstr,
            $errfile,
            $errline
        );
        
        error_log($logMessage, 3, self::$logPath . '/error.log');

        // In development, we let PHP's built-in display handle it since we enabled display_errors
        // But for a fully custom display, you could add the HTML output here inside an environment check.

        return true; // Don't execute PHP internal error handler.
    }

    /**
     * Custom exception handler.
     */
    public static function handleException(Throwable $exception): void
    {
        $logMessage = sprintf(
            "[%s] Uncaught Exception: %s in %s on line %d\n%s\n",
            date('Y-m-d H:i:s'),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );

        error_log($logMessage, 3, self::$logPath . '/exception.log');

        if (self::$environment === 'development') {
            echo "<div style='background-color: #ffebee; border: 1px solid #ef5350; padding: 15px; margin: 15px; font-family: sans-serif;'>";
            echo "<h2>Uncaught Exception</h2>";
            echo "<p><strong>Message:</strong> " . htmlspecialchars($exception->getMessage()) . "</p>";
            echo "<p><strong>File:</strong> " . $exception->getFile() . " on line " . $exception->getLine() . "</p>";
            echo "<h3>Stack Trace:</h3><pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>";
            echo "</div>";
        } else {
            // You can optionally create a nice 500 error page
            http_response_code(500);
            echo "<h1>Application Error</h1>";
            echo "<p>A critical error occurred. Please try again later. Details have been logged.</p>";
        }
    }
}