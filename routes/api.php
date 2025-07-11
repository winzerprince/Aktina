<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Java microservice callback routes
Route::post('/callbacks/pdf-processing/{applicationId}', function (Request $request, $applicationId) {
    $applicationService = app(\App\Interfaces\Services\ApplicationServiceInterface::class);

    $validated = $request->validate([
        'score' => 'required|numeric|min:0|max:100',
        'analysis' => 'required|string',
        'success' => 'required|boolean',
        'error' => 'nullable|string'
    ]);

    if ($validated['success']) {
        $applicationService->updateScore($applicationId, $validated['score'], $validated['analysis']);
    } else {
        $applicationService->markProcessingFailed($applicationId, $validated['error'] ?? 'Unknown error');
    }

    return response()->json(['status' => 'processed']);
})->middleware('throttle:600,1');
