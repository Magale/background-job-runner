<?php

function runBackgroundJob($class, $method, $params = []) {
    $paramString = implode(' ', array_map('escapeshellarg', $params));
    $command = PHP_OS_FAMILY === 'Windows'
        ? "start /B php run-job.php " . escapeshellarg($class) . " " . escapeshellarg($method) . " $paramString"
        : "php run-job.php " . escapeshellarg($class) . " " . escapeshellarg($method) . " $paramString > /dev/null 2>&1 &";

    exec($command);
}

