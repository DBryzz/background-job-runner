<?php

namespace App\Jobs;

class BackgroundJobs
{
    protected $attempts = 0;

    public function automaticDebitSuccess($amount, $motive)
    {
        // Simulate some processing
        return "Success: Debit: $amount | Motive: $motive";
    }

    public function automaticDebitFailuresThenSuccess($amount, $motive)
    {
        $this->attempts++;
        if ($this->attempts < 3) {
            throw new \Exception("Simulated failure on attempt {$this->attempts}");
        }
        return "Success: Debit: $amount | Motive: $motive";
    }
    public function automaticDebitFailure($amount, $motive)
    {
        $this->attempts++;
        if ($this->attempts < 3) {
            throw new \Exception("Simulated failure on attempt {$this->attempts}");
        }

        // Simulate some processing
        return "Failure: Debit: $amount | Motive: $motive";
    }

    public function unauthorizedDebitMethod()
    {
        return "Unauthorized Job";
    }
}
