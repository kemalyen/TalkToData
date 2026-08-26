<?php

namespace App\Livewire;

use App\Models\Conversation;
use Livewire\Attributes\On;
use Livewire\Component;

class DatasetManager extends Component
{
    public ?Conversation $activeConversation = null;

    public function mount()
    {
        $this->activeConversation = null;
    }

    public function startChat($datasetId)
    {
        $dataset = auth()->user()->datasets()->findOrFail($datasetId);

        $this->activeConversation = $dataset->conversations()->create([
            'user_id' => auth()->id(),
            'title' => 'Analysis - ' . now()->format('Y-m-d H:i'),
        ]);
    }

    #[On('datasetUploaded')]
    public function refreshDatasets()
    {
        // render() reloads datasets on each render
    }

    public function render()
    {
        $datasets = auth()->user()->datasets()->latest()->get();

        return view('livewire.dataset-manager', ['datasets' => $datasets]);
    }
}
