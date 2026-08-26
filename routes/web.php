<?php

use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DatasetController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DatasetController::class, 'index'])->name('home');

    // Datasets
    Route::get('/datasets', [DatasetController::class, 'index'])->name('datasets.index');
    Route::post('/datasets/upload', [DatasetController::class, 'store'])->name('datasets.store');
    Route::delete('/datasets/{dataset}', [DatasetController::class, 'destroy'])->name('datasets.destroy');

    // Conversations / Chat Interface
    Route::post('/datasets/{dataset}/conversations', [ConversationController::class, 'store'])->name('conversations.store');
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
});

require __DIR__.'/settings.php';
