<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmissionRequest;
use App\Jobs\ProcessSubmission;
use Illuminate\Http\Response;

class SubmissionController extends Controller
{
    public function submit(SubmissionRequest $request)
    {
        $validatedData = $request->validated();

        ProcessSubmission::dispatch($validatedData);

        return response()->json(['message' => 'Submission received and is being processed.'], Response::HTTP_ACCEPTED);
    }
}
