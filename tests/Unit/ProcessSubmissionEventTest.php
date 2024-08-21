<?php

namespace Tests\Unit;

use App\Events\SubmissionSaved;
use App\Jobs\ProcessSubmission;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ProcessSubmissionEventTest extends TestCase
{

    public function test_submission_job_is_working_fine()
    {
        Event::fake();

        $inputData = [
            'name' => 'John Doe data_is_writing_to_logs',
            'email' => 'john.doe@example.com',
            'message' => 'This is a test message.',
        ];

        (new ProcessSubmission($inputData))->handle();

        Event::assertDispatched(SubmissionSaved::class, function (SubmissionSaved $event) use ($inputData) {
            self::assertArrayIsEqualToArrayIgnoringListOfKeys(
                $inputData,
                $event->submission->toArray(),
                ['created_at', 'updated_at', 'id']
            );
            return true;
        });

        $this->assertDatabaseHas('submissions', $inputData);
    }
}
