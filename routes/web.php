<?php

use App\Http\Controllers\ConceptController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\InterviewGenerationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('domains', DomainController::class);

    Route::prefix('domains/{domain}/concepts')->group(function () {
        Route::get('/archived', [ConceptController::class, 'archived'])->name('domains.concepts.archived');
        Route::patch('/{concept}/status', [ConceptController::class, 'updateStatus'])->name('domains.concepts.updateStatus');
    });

    Route::prefix('concepts')->group(function () {
        Route::patch('/{concept}/restore', [ConceptController::class, 'restore'])->name('concepts.restore');
        Route::delete('/{concept}/force-delete', [ConceptController::class, 'forceDelete'])->name('concepts.forceDelete');
    });

    Route::resource('domains.concepts', ConceptController::class);

    Route::post('/concepts/{concept}/generations', [InterviewGenerationController::class, 'store'])->name('generations.store');
    Route::delete('/generations/{generation}', [InterviewGenerationController::class, 'destroy'])->name('generations.destroy');
});

require __DIR__.'/auth.php';
