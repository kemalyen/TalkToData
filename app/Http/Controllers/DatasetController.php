<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DatasetController extends Controller
{
    public function index()
    {
        $datasets = auth()->user()->datasets()->latest()->get();
         
        return view('datasets.index', compact('datasets'));
    }

    public function store(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt|max:20480']);

        // 1. Store file in storage/app/private/datasets
        $path = $request->file('file')->store('datasets', 'local');

        // 2. Call Python FastAPI to profile dataset schema
        $pythonResponse = Http::post(config('services.python_engine.url') . '/profile-dataset', [
            'file_path' => storage_path("app/private/{$path}")
        ]);

        // 3. Create Dataset record
        $dataset = auth()->user()->datasets()->create([
            'name' => $request->file('file')->getClientOriginalName(),
            'file_path' => $path,
            'schema_json' => $pythonResponse->json('schema'),
            'row_count' => $pythonResponse->json('row_count', 0),
        ]);

        return redirect()->route('datasets.index')->with('success', 'Dataset uploaded and profiled!');
    }
}