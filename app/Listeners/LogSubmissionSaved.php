<?php

namespace App\Listeners;

use App\Events\SubmissionSaved;
use Illuminate\Support\Facades\Log;

class LogSubmissionSaved
{
    /**
     * Handle the event.
     *
     * @param  SubmissionSaved  $event
     * @return void
     */
    public function handle(SubmissionSaved $event): void
    {
        Log::info('Submission saved successfully', [
            'name' => $event->submission->name,
            'email' => $event->submission->email,
        ]);
    }
}
