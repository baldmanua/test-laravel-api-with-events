<?php

namespace App\Jobs;

use App\Events\SubmissionSaved;
use App\Models\Submission;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSubmission implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $submissionData;

    /**
     * Create a new job instance.
     */
    public function __construct(array $submissionData)
    {
        $this->submissionData = $submissionData;
    }

    public function getSubmissionData(): array
    {
        return $this->submissionData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Save submission to the database
            $submission = Submission::create($this->submissionData);

            // Trigger the SubmissionSaved event
            event(new SubmissionSaved($submission));
        } catch (Exception $e) {
            Log::error('Submission failed: ' . $e->getMessage(), $e->getTrace());
        }
    }
}
