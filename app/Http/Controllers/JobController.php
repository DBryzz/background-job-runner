<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JobController extends Controller
{
    public function runJob($className, $methodName, Request $request)
    {

        // Validate class and method names
        $allowedJobs = config('jobs.allowed_jobs');
        $classPath = 'App\Jobs\\'.$className;

        if (!array_key_exists($classPath, $allowedJobs)) {
            return response()->json(['error' => 'Unauthorized class: ' .$className], 403);
        }

        if (!in_array($methodName, $allowedJobs[$classPath])) {
            return response()->json(['error' => 'Unauthorized method: ' . $methodName . ' on class: ' . $className], 403);
        }

        // Get query parameters
        $args = $request->query('arguments', []);
        $delay = $request->query('delay', 0);
        $retries = $request->query('retries', 1);
        $priority = $request->query('priority', 'NORMAL');

        try {
            // Execute the job in the background
            runBackgroundJob($classPath, $methodName, $retries, $priority, $delay, ...$args);
            return response()->json(['message' => 'Job started successfully. Please check logs']);
        } catch(\Throwable $e) {
            return response()->json(['message' => 'Job failed. ' . $e->getMessage()]);
        }
    }


}
