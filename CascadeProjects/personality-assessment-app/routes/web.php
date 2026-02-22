<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssessmentController;

Route::get('/', function () {
    return redirect()->route('assessment.index');
});

// Assessment routes
Route::prefix('assessment')->name('assessment.')->group(function () {
    Route::get('/', [AssessmentController::class, 'index'])->name('index');
    Route::post('/analyze', [AssessmentController::class, 'analyze'])->name('analyze');
    Route::get('/results/{profile}', [AssessmentController::class, 'results'])->name('results');
});
