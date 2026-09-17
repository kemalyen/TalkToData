<?php

namespace App\Livewire;

use App\Ai\Tools\ExecutePythonAnalysisTool;
use App\Models\Conversation;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Throwable;

use function Laravel\Ai\agent;

class ChatBot extends Component
{
    public Conversation $conversation;

    public string $prompt = '';

    public function sendMessage(): void
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

        try {
            $response = $agent->prompt(
                $userQuery,
                timeout: 120
            );
        } catch (Throwable $exception) {
            Log::error('Agent request failed.', ['exception' => $exception]);

            $this->conversation->messages()->create([
                'role' => 'assistant',
                'content' => 'I could not complete the analysis. Please try again.',
            ]);

            return;
        }

        Log::debug('Agent response received.', [
            'text' => $response->text,
            'toolResults' => $response->toolResults->toArray(),
            'lastToolResult' => $response->toolResults->last()?->result,
        ]);
        $toolResult = $response->toolResults->last()?->result;
        $analysis = is_string($toolResult) ? json_decode($toolResult, true) : $toolResult;
        $analysis = is_array($analysis) ? $analysis : [];
        $hasSuccessfulAnalysis = ($analysis['status'] ?? null) === 'success';
        Log::debug('Analysis result processed.', [
            'analysis' => $analysis,
            'hasSuccessfulAnalysis' => $hasSuccessfulAnalysis,
        ]);
        if (! $hasSuccessfulAnalysis) {
            $content = $analysis['reason'] ?? 'I can only answer questions about this uploaded dataset.';
        } else {
            $content = trim($response->text);
        }

        if ($hasSuccessfulAnalysis && $content === '') {
            $content = $this->analysisFallback($analysis);
        }

        Log::debug('Agent response received.', [
            'text' => $content,
            'analysis' => $analysis,
        ]);

        // 3. Save assistant response
        $this->conversation->messages()->create([
            'role' => 'assistant',
            'content' => $content,
            'chart_payload' => $analysis ?: null,
        ]);
    }

    /**
     * @param  array<string, mixed>  $analysis
     */
    private function analysisFallback(array $analysis): string
    {
        if (($analysis['status'] ?? null) !== 'success') {
            return 'The analysis service did not return a usable result.';
        }

        $columnNames = [];
        $columns = $analysis['columns'] ?? [];

        if (is_array($columns)) {
            foreach ($columns as $column) {
                if (is_array($column) && is_string($column['name'] ?? null)) {
                    $columnNames[] = $column['name'];
                }
            }
        }

        $lines = [
            'Dataset summary',
            'Total rows: '.($analysis['row_count'] ?? 0),
            'Columns: '.implode(', ', $columnNames),
        ];

        $numericSummary = $analysis['numeric_summary'] ?? [];

        if (is_array($numericSummary)) {
            foreach ($numericSummary as $column => $metrics) {
                if (! is_array($metrics)) {
                    continue;
                }

                $lines[] = sprintf(
                    '%s: min %s, max %s, mean %s, sum %s',
                    $column,
                    $metrics['min'] ?? 'n/a',
                    $metrics['max'] ?? 'n/a',
                    $metrics['mean'] ?? 'n/a',
                    $metrics['sum'] ?? 'n/a',
                );
            }
        }

        return implode("\n", $lines);
    }

    public function render(): View
    {
        return view('livewire.chat-bot', [
            'messages' => $this->conversation->messages()->orderBy('created_at')->get(),
        ]);
    }
}
