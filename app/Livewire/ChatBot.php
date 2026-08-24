<?php

namespace App\Livewire;

use App\Ai\Tools\ExecutePythonAnalysisTool;
use App\Models\Conversation;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

use function Laravel\Ai\agent;

class ChatBot extends Component
{
    public Conversation $conversation;

    public string $prompt = '';

    public function sendMessage()
    {
        $this->validate(['prompt' => 'required|string']);

        // 1. Save user message
        $this->conversation->messages()->create([
            'role' => 'user',
            'content' => $this->prompt,
        ]);

        $userQuery = $this->prompt;
        $this->prompt = '';

        // 2. Invoke Laravel AI Agent with custom Python execution tool
        $agent = agent(
            instructions: "You are a Data Analyst Agent. Analyze dataset ID: {$this->conversation->dataset_id}. Answer questions clearly and provide visualizations when suitable.",
            tools: [new ExecutePythonAnalysisTool]
        );

        $response = $agent->prompt(
            $userQuery,
            provider: 'gemini',
            model: env('GEMINI_TEXT_MODEL', 'gemini-3.1-flash-lite'),
            timeout: 120,
        );
        Log::debug("Agent response: " . $response->text);

        // 3. Save assistant response
        $this->conversation->messages()->create([
            'role' => 'assistant',
            'content' => $response->text,
        ]);
    }

    public function render()
    {
        return view('livewire.chat-bot', [
            'messages' => $this->conversation->messages()->orderBy('created_at')->get(),
        ]);
    }
}
