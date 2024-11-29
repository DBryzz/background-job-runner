<?php

namespace App\Jobs;

class SendNotificationJobs
{
    protected $attempts = 0;

    public function pickUp($driver, $time, $location)
    {
        // Simulate some processing
        return "Success: $driver pick me up at $time at $location.";
    }

    public function dateWithMe($to, $location, $time)
    {
        // Simulate some processing
        return "Success: $to meet me for a date at $location at $time";
    }

    public function saveMe($from)
    {
        return "Success: SOS @$from";
    }
}
