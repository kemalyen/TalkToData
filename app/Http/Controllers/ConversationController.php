<?php

namespace App\Http\Controllers;
 

use App\Models\Conversation;
use App\Models\Dataset;

class ConversationController extends Controller
{
    public function store(Dataset $dataset)
    {
        $conversation = $dataset->conversations()->create([
            'user_id' => auth()->id(),
            'title' => 'Analysis - ' . now()->format('Y-m-d H:i'),
        ]);

        return redirect()->route('conversations.show', $conversation);
    }

    public function show(Conversation $conversation)
    {
        $this->authorize('view', $conversation);
        return view('conversations.show', compact('conversation'));
    }
}