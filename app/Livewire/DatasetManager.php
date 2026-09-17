<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\Dataset;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class DatasetManager extends Component
{
    public ?Conversation $activeConversation = null;

    public function mount(): void
    {
        $this->activeConversation = null;
    }

    public function startChat(int $datasetId): void
    {
        $dataset = Dataset::query()
            ->where('user_id', auth()->id())
            ->findOrFail($datasetId);

        $this->activeConversation = $dataset->conversations()->create([
            'user_id' => auth()->id(),
            'title' => 'Analysis - '.now()->format('Y-m-d H:i'),
        ]);
    }

    #[On('datasetUploaded')]
    public function refreshDatasets(): void
    {
        // render() reloads datasets on each render
    }

    public function render(): View
    {
        $datasets = auth()->user()->datasets()->latest()->get();

        return view('livewire.dataset-manager', ['datasets' => $datasets]);
    }
}
