<?php

namespace App\Livewire;

use Flux\Flux;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class DatasetUpload extends Component
{
    use WithFileUploads;

    public $file;

    public $fileName = '';

    protected function rules(): array
    {
        return [
            'file' => 'required|file|mimes:csv,txt|max:20480',
        ];
    }

    public function updatedFile($value)
    {
        if ($value) {
            $this->fileName = $value->getClientOriginalName();
        }
    }

    public function upload()
    {
        $this->validate();

        $path = $this->file->store('datasets', 'local');

        Log::info('Stored file at: '.storage_path("app/private/{$path}"));

        $pythonResponse = Http::post(config('services.python_engine.url').'/profile-dataset', [
            'file_path' => storage_path("app/private/{$path}"),
        ]);

        auth()->user()->datasets()->create([
            'name' => $this->file->getClientOriginalName(),
            'file_path' => $path,
            'schema_json' => $pythonResponse->json('schema'),
            'row_count' => $pythonResponse->json('row_count', 0),
        ]);

        $this->reset(['file', 'fileName']);

        $this->dispatch('datasetUploaded');
        Flux::toast('Dataset uploaded and profiled!', variant: 'success');
    }

    public function render()
    {
        return view('livewire.dataset-upload');
    }
}
