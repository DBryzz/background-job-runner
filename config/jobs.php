<?php

return [
    'allowed_jobs' => [
        'App\Jobs\BackgroundJobs' => ['automaticDebitSuccess', 'automaticDebitFailuresThenSuccess', 'automaticDebitFailure'],
        'App\Jobs\SendNotificationJobs' => ['pickUp', 'dateWithMe', 'saveMe'],
        // Add more allowed jobs and methods here
    ],
];
