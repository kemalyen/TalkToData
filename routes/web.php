<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\ConversationController;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::middleware(['auth'])->group(function () {
    // Datasets
    Route::get('/datasets', [DatasetController::class, 'index'])->name('datasets.index');
    Route::post('/datasets/upload', [DatasetController::class, 'store'])->name('datasets.store');
    Route::delete('/datasets/{dataset}', [DatasetController::class, 'destroy'])->name('datasets.destroy');

    // Conversations / Chat Interface
    Route::post('/datasets/{dataset}/conversations', [ConversationController::class, 'store'])->name('conversations.store');
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
});
require __DIR__.'/settings.php';
