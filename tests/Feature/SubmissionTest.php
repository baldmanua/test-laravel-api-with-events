<?php

namespace Tests\Feature;

use App\Events\SubmissionSaved;
use App\Jobs\ProcessSubmission;
use App\Models\Submission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SubmissionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function submission_data_is_being_validated()
    {
        $response = $this->postJson('/api/submit', [
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'message']);
    }

    /** @test */
    public function request_dispatches_a_submission_job()
    {
        Queue::fake();

        $inputData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'message' => 'This is a test message.',
        ];

        $response = $this->postJson('/api/submit', $inputData);

        $response->assertStatus(202);

        Queue::assertPushed(ProcessSubmission::class, function (ProcessSubmission $job) use ($inputData) {
            self::assertEquals($job->getSubmissionData(), $inputData);
            return true;
        });
    }

    /** @test */
    public function submission_job_is_working_fine()
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

    /** @test */
    public function success_data_is_writing_to_logs()
    {
        $inputData = [
            'name' => 'John Doe success_data_is_writing_to_logs',
            'email' => 'john.doe@example.com'
        ];
        Log::shouldReceive('info')->with('Submission saved successfully', $inputData)->once();

        event(new SubmissionSaved(new Submission($inputData)));

    }
}
