<?php

require __DIR__ . '/vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Create logger
$log = new Logger('background_jobs');
$log->pushHandler(new StreamHandler(__DIR__ . '/storage/logs/background_jobs.log', Logger::DEBUG));
$errorLog = new StreamHandler(__DIR__ . '/storage/logs/background_jobs_errors.log', Logger::ERROR);
$log->pushHandler($errorLog);

// Load retry configuration
$retryConfig = require __DIR__ . '/config/job-retry.php';
$maxAttempts = $retryConfig['max_attempts'] ?? 1;
$delay = $retryConfig['delay_seconds'] ?? 0;

$class = $argv[1] ?? null;
$method = $argv[2] ?? null;
$params = array_slice($argv, 3);

if (!$class || !$method) {
    echo "Job failed: Class and method must be specified.\n";
    exit(1);
}

$class = "Jobs\\" . $class;

// Load allowed job configuration
$allowedJobs = require __DIR__ . '/config/background-jobs.php';

// Security check: only allow approved classes and methods
if (!isset($allowedJobs[$class]) || !in_array($method, $allowedJobs[$class])) {
    echo "Job failed: Execution of $class::$method is not allowed.\n";
    exit(1);
}

$attempt = 0;
while ($attempt < $maxAttempts) {
    try {
        $attempt++;

        $log->info("Running job (attempt $attempt): $class::$method", ['params' => $params]);

        if (!class_exists($class)) {
            throw new Exception("Class $class does not exist.");
        }

        $instance = new $class();

        if (!method_exists($instance, $method)) {
            throw new Exception("Method $method does not exist in class $class.");
        }

        call_user_func_array([$instance, $method], $params);

        $log->info("Job completed: $class::$method");
        echo "Job executed successfully on attempt #$attempt.\n";
        break;

    } catch (Throwable $e) {
        $log->error("Job failed on attempt #$attempt", [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        if ($attempt >= $maxAttempts) {
            echo "Job failed after $attempt attempts: " . $e->getMessage() . "\n";
            exit(1);
        }

        echo "Retrying in {$delay} seconds...\n";
        sleep($delay);
    }
}

