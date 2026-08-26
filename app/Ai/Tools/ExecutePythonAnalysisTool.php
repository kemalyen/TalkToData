<?php

namespace App\Ai\Tools;

use App\Models\Dataset;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Http;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

class ExecutePythonAnalysisTool implements Tool
{
    public function description(): string
    {
        return 'Executes Pandas code against a specified CSV dataset via the Python microservice to generate table results or chart configurations.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'dataset_id' => $schema->integer()->description('The ID of the dataset uploaded by the user.')->required(),
            'user_query' => $schema->string()->description('The natural language query or intent for the analysis.')->required(),
        ];
    }

    public function handle(Request $request): string
    {
        $datasetId = $request->integer('dataset_id');
        $query = $request->string('user_query');

      //  Log::debug("Executing Python analysis for dataset ID: {$datasetId} with query: {$query}");

        // 1. Fetch file path & schema summary stored in Laravel
        $dataset = Dataset::findOrFail($datasetId);

        // 2. Call the Python FastAPI microservice
        $response = Http::timeout(10)
            ->baseUrl(config('services.python_engine.url', 'http://127.0.0.1:8090'))
            ->post('/analyze', [
                'file_path' => storage_path("app/private/{$dataset->file_path}"),
                'schema_json' => $dataset->schema_json,
                'prompt' => $query,
            ]);
 
        if ($response->failed()) {
            return 'Failed to execute analysis: '.$response->body();
        }

        // 3. Return the JSON string result back to the LLM agent
        return $response->body();
    }
}
