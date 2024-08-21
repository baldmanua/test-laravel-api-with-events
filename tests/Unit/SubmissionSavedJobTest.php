<?php

namespace Tests\Unit;

use App\Events\SubmissionSaved;
use App\Models\Submission;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class SubmissionSavedJobTest extends TestCase
{
    public function test_success_data_is_writing_to_logs()
    {
        $inputData = [
            'name' => 'John Doe success_data_is_writing_to_logs',
            'email' => 'john.doe@example.com'
        ];
        Log::shouldReceive('info')->with('Submission saved successfully', $inputData)->once();

        event(new SubmissionSaved(new Submission($inputData)));

    }
}
