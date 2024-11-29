# Laravel Background Job Runner System

This project implements a background job runner system in a Laravel application, allowing you to manage jobs efficiently with features like retry attempts, delays, job priorities, and a web dashboard for monitoring active jobs.

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
    - [Allowed Jobs](#allowed-jobs)
    - [Configurations](#confugurations)
    - [Run Using the Terminal](#run-using-the-terminal)
    - [Run Using the Web](#run-using-the-web)
    - [Logs](#logs)
- [Assumptions and Limitations](#assumptions-and-limitations)
- [Potential Improvements](#potential-improvements)

## Requirements

- PHP >= 8.4
- Laravel >= 10
- Composer

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/DBryzz/background-job-runner.git
   cd background-job-runner.git
   git checkout main

## Usage

### Allowed Jobs
Jobs are executed by passing the job's className and method. Hence, we have the following allowed class and methods
```
'App\Jobs\BackgroundJobs' => [
                                'automaticDebitSuccess', 
                                'automaticDebitFailuresThenSuccess', 
                                'automaticDebitFailure'
                              ],
'App\Jobs\SendNotificationJobs' => [
                                        'pickUp', 
                                        'dateWithMe', 
                                        'saveMe'
                                    ],
```

### Confugurations
Use the following flags to simulate delay, retry and priority
    
| Flag    | Description                          | Type  | default |
|-----|--------------------------------------|-------|---------|
|  --retries   | Number of retries in case of failure | int   | 1       |
|  --delay   | Set job delay                        | int   | 0       |
|  --priority   | Set priority                         | string | NORMAL  |

### Run Using the Terminal
- You can use laravel artisan to run the jobs without necessarily starting laravel server
   ```bash
   php artisan app:runBgJob App\\Jobs\\BackgroundJobs automaticDebitSuccess 1000 "Paypal Online Purchase" --priority=HIGH --delay=5 
   php artisan app:runBgJob App\\Jobs\\BackgroundJobs automaticDebitFailure 1000 "Paypal Online Purchase" --retries=4 --priority=NORMAL --delay=5 
   php artisan app:runBgJob App\\Jobs\\BackgroundJobs automaticDebitFailuresThenSuccess 1000 "Paypal Online Purchase" --retries=4 --priority=HIGH --delay=0 
   php artisan app:runBgJob App\\Jobs\\SendNotificationJobs pickUp "Tony" "2PM" "Central Park"  --priority=LOW --delay=1 
   php artisan app:runBgJob App\\Jobs\\SendNotificationJobs dateWithMe "Tony" "Central Park" "2PM"  --retries=1 --priority=NORMAL --delay=5 
   php artisan app:runBgJob App\\Jobs\\SendNotificationJobs saveMe "Tony Stark"  --priority=HIGH --delay=5
    

### Run Using the web.
- Run serve command
    ```bash
    php artisan serve
- Using the browser go to
    ```
    http://localhost:8000/run-job/{className}/{methodName}?arguments[]=value1&arguments[]=value2&delay=int&retries=int&priority=string
    ```
- Examples
  ```bash
  http://localhost:8000/run-job/BackgroundJobs/automaticDebitSuccess?arguments[]=2000&arguments[]=Registration&delay=2&priority=HIGH
  http://localhost:8000/run-job/BackgroundJobs/automaticDebitFailure?arguments[]=2000&arguments[]=Registration&delay=2&retries=3&priority=NORMAL
  http://localhost:8000/run-job/BackgroundJobs/automaticDebitFailuresThenSuccess?arguments[]=2000&arguments[]=invitation&delay=2&retries=4&priority=LOW
  http://localhost:8000/run-job/SendNotificationJobs/pickUp?arguments[]=Jane&arguments[]=6PM&arguments[]=LaLa Land&delay=5&priority=LOW
  http://localhost:8000/run-job/SendNotificationJobs/dateWithMe?arguments[]=John&arguments[]=Space X Restaurant&arguments[]=8PM&delay=4&priority=NORMAL
  http://localhost:8000/run-job/SendNotificationJobs/saveMe?arguments[]=John Doe&delay=3&priority=HIGH

### Logs
To view the logs go to logs\job_execution.log or logs\background_jobs_errors.log
- Sample Log
```
  [2024-11-29 07:38:28] Class: App\Jobs\SendNotificationJobs, Method: saveMe, Priority: HIGH, Status: SUCCESS, Message: Success: SOS @John Doe
  [2024-11-29 07:39:38] Class: App\Jobs\SendNotificationJobs, Method: dateWithMe, Priority: NORMAL, Status: SUCCESS, Message: Success: John meet me for a date at Space X Restaurant at 8PM
```
- Sample error log
```
  [2024-11-29 07:36:40] Class: App\Jobs\BackgroundJobs, Method: automaticDebitFailure, Priority: NORMAL, Status: FAILED, Message: Simulated failure on attempt 1
  [2024-11-29 07:36:43] Class: App\Jobs\BackgroundJobs, Method: automaticDebitFailure, Priority: NORMAL, Status: FAILED, Message: Simulated failure on attempt 1
```

## Assumptions and Limitations
- Only the allowed jobs can be executed. 
- Jobs are executed with all required arguments

## Potential Improvements
- Build a user interface to better visualize jobs
- Add database to persist jobs and manage them by priority
- Allow dynamic job creation
