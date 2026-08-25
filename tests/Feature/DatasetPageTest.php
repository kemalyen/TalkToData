<?php

use App\Livewire\DatasetManager;
use App\Models\Dataset;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('renders the dataset manager page', function () {
    Livewire::test(DatasetManager::class)
        ->assertSee('Upload Dataset')
        ->assertSee('No dataset selected');
});

it('shows datasets as pills', function () {
    Dataset::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'sales_data.csv',
        'schema_json' => ['columns' => [['name' => 'revenue'], ['name' => 'cost']]],
    ]);

    Livewire::test(DatasetManager::class)
        ->assertSee('sales_data.csv')
        ->assertSee('revenue')
        ->assertSee('Chat');
});

it('starts a chat and renders the chat bot component', function () {
    $dataset = Dataset::create([
        'user_id' => $this->user->id,
        'name' => 'sales.csv',
        'file_path' => 'datasets/sales.csv',
        'schema_json' => ['columns' => [['name' => 'amount', 'type' => 'int64']]],
        'row_count' => 3,
    ]);

    Livewire::test(DatasetManager::class)
        ->call('startChat', $dataset->id)
        ->assertSeeLivewire('chat-bot');
});

it('creates a conversation when starting a chat', function () {
    $dataset = Dataset::create([
        'user_id' => $this->user->id,
        'name' => 'metrics.csv',
        'file_path' => 'datasets/metrics.csv',
        'schema_json' => ['columns' => [['name' => 'value', 'type' => 'float']]],
        'row_count' => 10,
    ]);

    Livewire::test(DatasetManager::class)
        ->call('startChat', $dataset->id);

    $this->assertDatabaseHas('conversations', [
        'dataset_id' => $dataset->id,
        'user_id' => $this->user->id,
    ]);
});
