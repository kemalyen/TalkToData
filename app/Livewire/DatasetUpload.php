<?php

namespace App\Livewire;

use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class DatasetUpload extends Component
{
    use WithFileUploads;

    public ?TemporaryUploadedFile $file = null;

    public string $fileName = '';

    /**
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            'file' => 'required|file|mimes:csv,txt|max:20480',
        ];
    }

    public function updatedFile(?TemporaryUploadedFile $value): void
    {
        if ($value) {
            $this->fileName = $value->getClientOriginalName();
        }
    }

    public function processUpload(): void
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

    public function render(): View
    {
        return view('livewire.dataset-upload');
    }
}
