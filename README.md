# Background Job Runner

This project provides a custom system to run PHP background jobs outside of Laravel's built-in queue system.

## Features

- CLI runner for PHP classes and methods
- Global helper to trigger jobs
- Logging of job status and errors
- Retry mechanism with configurable attempts and delays
- Approved job list for security

## Usage

### Run a Job via CLI

```bash
php run-job.php SampleJob execute "param1" "param2"
