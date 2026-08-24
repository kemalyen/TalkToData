<?php

use App\Ai\Tools\ExecutePythonAnalysisTool;
use App\Models\Dataset;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Laravel\Ai\Tools\Request;

it('forwards the dataset analysis request to python and returns its response', function () {
    $user = User::factory()->create();
    $dataset = Dataset::create([
        'user_id' => $user->id,
        'name' => 'sales.csv',
        'file_path' => 'datasets/sales.csv',
        'schema_json' => ['columns' => [['name' => 'amount', 'type' => 'int64']]],
        'row_count' => 3,
    ]);

    Http::fake([
        '*' => Http::response([
            'status' => 'success',
            'row_count' => 3,
            'numeric_summary' => ['amount' => ['sum' => 60]],
        ]),
    ]);

    $result = (new ExecutePythonAnalysisTool)->handle(new Request([
        'dataset_id' => $dataset->id,
        'user_query' => 'Summarize amount totals',
    ]));

    expect(json_decode($result, true))->toMatchArray([
        'status' => 'success',
        'row_count' => 3,
    ]);

    Http::assertSent(function ($request) use ($dataset): bool {
        return $request->url() === config('services.python_engine.url') . '/analyze'
            && $request['file_path'] === storage_path("app/private/{$dataset->file_path}")
            && $request['schema_json'] === $dataset->schema_json
            && $request['prompt'] === 'Summarize amount totals';
    });
});

it('returns the python error response instead of hiding it', function () {
    $user = User::factory()->create();
    $dataset = Dataset::create([
        'user_id' => $user->id,
        'name' => 'sales.csv',
        'file_path' => 'datasets/sales.csv',
        'schema_json' => ['columns' => []],
        'row_count' => 0,
    ]);

    Http::fake(['*' => Http::response(['detail' => 'File not found'], 404)]);

    $result = (new ExecutePythonAnalysisTool)->handle(new Request([
        'dataset_id' => $dataset->id,
        'user_query' => 'Summarize this dataset',
    ]));

    expect($result)->toContain('Failed to execute Python analysis')
        ->and($result)->toContain('File not found');
});
