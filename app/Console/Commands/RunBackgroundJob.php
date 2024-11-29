<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;

class RunBackgroundJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:runBgJob {class} {method} {params?*} {--retries=1 : Number of retry attempts} {--priority=NORMAL : Job priority} {--delay=0 : Delay of a job in seconds}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a specified class method in the background';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $className = $this->argument('class');
        $methodName = $this->argument('method');
        $params = $this->argument('params');
        $maxRetries = (int) $this->option('retries');
        $priority = (string) $this->option('priority');
        $delay = (int) $this->option('delay');

        // Load the allowed jobs configuration
        $allowedJobs = config('jobs.allowed_jobs');

        // Validate class
        if (!array_key_exists($className, $allowedJobs)) {
            $this->error("Unauthorized class: $className");
            return;
        }

        // Validate method names
        if (!in_array($methodName, $allowedJobs[$className])) {
            $this->error("Unauthorized method: $methodName on class: $className");
            return;
        }


        // Define the log file path
        $executionLogFile = storage_path('logs/job_execution.log');
        $errorLogFile = storage_path('logs/background_jobs_errors.log');

        $attempts = 0;

        do {
            $attempts++;
        try {
            // Instantiate the class
            $instance = new $className();

            // Simulate a long-running task
            sleep($delay);

            // Call the method with parameters
            $result = call_user_func_array([$instance, $methodName], $params);

            $isSuccess = $this->logExecution($executionLogFile, $className, $methodName, $priority, 'SUCCESS', $result);
            $this->info("Job executed successfully: $result");
            return $isSuccess;
        } catch (\Throwable $e) {
            $isSuccess = $this->logExecution($errorLogFile, $className, $methodName, $priority,'FAILED', $e->getMessage());
            $this->error("Job execution failed: " . $e->getMessage());
            sleep(1);
            if($attempts == $maxRetries)
            return $isSuccess;
        }
        } while ($attempts < $maxRetries);
        $this->error("All attempts failed.");
    }

    // Function to log execution details
    public function logExecution($logFile, $className, $methodName, $priority, $status, $message = ''): bool
    {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] Class: $className, Method: $methodName, Priority: $priority, Status: $status";
        if ($message) {
            $logEntry .= ", Message: $message";
        }
        $logEntry .= "\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND);

        return $status == "SUCCESS";
    }
}
