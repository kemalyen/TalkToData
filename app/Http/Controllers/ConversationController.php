<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Dataset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;


class ConversationController extends Controller
{
    public function store(Dataset $dataset): RedirectResponse
    {
        $conversation = $dataset->conversations()->create([
            'user_id' => auth()->id(),
            'title' => 'Analysis - ' . now()->format('Y-m-d H:i'),
        ]);

        return redirect()->route('conversations.show', $conversation);
    }

    public function show(Conversation $conversation): View
    {
        //$this->authorize('view', $conversation);
        return view('conversations.show', compact('conversation'));
    }
}
