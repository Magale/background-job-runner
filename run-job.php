<?php

require __DIR__ . '/vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Create logger
$log = new Logger('background_jobs');
$log->pushHandler(new StreamHandler(__DIR__ . '/storage/logs/background_jobs.log', Logger::DEBUG));
$errorLog = new StreamHandler(__DIR__ . '/storage/logs/background_jobs_errors.log', Logger::ERROR);
$log->pushHandler($errorLog);

try {
    $class = $argv[1] ?? null;
    $method = $argv[2] ?? null;
    $params = array_slice($argv, 3);

    if (!$class || !$method) {
        throw new Exception("Class and method must be specified.");
    }

    // Log job start
    $log->info("Running job: $class::$method", ['params' => $params]);

    $class = "Jobs\\" . $class;  // Add the Jobs namespace dynamically
    //print_r(get_declared_classes());

    if (!class_exists($class)) {
        throw new Exception("Class $class does not exist.");
    }

    $instance = new $class();

    if (!method_exists($instance, $method)) {
        throw new Exception("Method $method does not exist in class $class.");
    }

    // Call the method with parameters
    call_user_func_array([$instance, $method], $params);

    // Log job completion
    $log->info("Job completed: $class::$method");
    echo "Job executed successfully.\n";

} catch (Throwable $e) {
    // Log errors
    $log->error("Job failed", [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    echo "Job failed: " . $e->getMessage() . "\n";
    exit(1);
}