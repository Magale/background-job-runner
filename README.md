
# Background Job Runner

This project provides a custom system to run PHP background jobs outside of Laravel's built-in queue system. It allows you to execute jobs via the command line, handle errors and retries, and log job status.

## Features

- **CLI Runner** for PHP classes and methods.
- **Global Helper** to trigger background jobs in Laravel.
- **Logging** of job execution status and errors.
- **Retry Mechanism** with configurable attempts and delays.
- **Approved Job List** for security, ensuring only pre-approved jobs can be executed.

## Requirements

- PHP 7.4+ 
- Laravel (for helper function integration)

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/Magale/background-job-runner.git
   cd background-job-runner
   ```
2. Install dependencies:
   ```bash
   composer install
   ```

## Usage

### Run a Job via CLI

You can run a job via the command line by specifying the class name, method name, and any parameters:

```bash
php run-job.php SampleJob execute "param1" "param2"
```

This will execute the `execute` method of the `SampleJob` class, passing `param1` and `param2` as arguments.

### Global Helper Function

You can trigger a background job using the global helper function in Laravel.

In your Laravel application, add the following code to trigger a job:

```php
runBackgroundJob('SampleJob', 'execute', ['param1', 'param2']);
```

This will execute the `SampleJob`'s `execute` method with the provided parameters.

## Configuration

### Configuring Allowed Jobs

In `config/background-jobs.php`, you can define which classes and methods are allowed to execute:

```php
return [
    'Jobs\SampleJob' => ['execute'],
];
```

This configuration ensures only `SampleJob` and its `execute` method can be executed. Any attempt to run other classes or methods will be rejected for security.

### Configuring Retry Attempts and Delays

You can configure the retry mechanism in `config/job-retry.php`:

```php
return [
    'attempts' => 3,  // Number of retry attempts
    'delay' => 5,      // Delay between attempts in seconds
];
```

### Logging

Logs are stored in the following locations:

- **Job status logs**: `storage/logs/background_jobs.log`
- **Error logs**: `storage/logs/background_jobs_errors.log`

### Handling Errors

All errors are logged in the error log file, including job failures and exceptions.

## Security

The system ensures only pre-approved classes and methods can be executed by checking the `config/background-jobs.php` file for valid jobs. Additionally, inputs are sanitized to mitigate security risks.

## Retry Mechanism

If a job fails, it will automatically be retried up to a specified number of attempts, with a delay between each retry. The retry configuration is stored in `config/job-retry.php`.

## Example

1. **Run Job with Retry:**
   ```bash
   php run-job.php SampleJob execute "param1" "param2"
   ```

2. **Helper Function in Laravel:**
   ```php
   runBackgroundJob('SampleJob', 'execute', ['param1', 'param2']);
   ```

## Conclusion

This system provides a custom and flexible way to run background jobs in PHP, with retry logic, logging, and security features built in.
