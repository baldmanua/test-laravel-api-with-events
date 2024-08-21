<?php

namespace Tests\Feature;

use App\Jobs\ProcessSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_submission_data_is_being_validated()
    {
        $response = $this->postJson('/api/submit', [
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'message']);
    }

    public function test_request_dispatches_a_submission_job()
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
}
