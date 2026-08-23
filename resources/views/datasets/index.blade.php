<x-layouts::app :title="__('Data Warehouse &amp; Datasets')">
    <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header Section -->
        <div class="md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                    Data Warehouse &amp; Datasets
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Upload your CSV files to profile schemas and run conversational AI analytics.
                </p>
            </div>
        </div>

        <!-- Success/Error Alert Messages -->
        @if (session('success'))
            <div class="rounded-md bg-green-50 p-4 border border-green-200">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-md bg-red-50 p-4 border border-red-200">
                <div class="flex">
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">There were errors with your submission:</h3>
                        <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Upload Form Card -->
        <div class="bg-white shadow rounded-lg p-6 border border-gray-200">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Upload New Dataset</h3>
            
            <form action="{{ route('datasets.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="flex items-center justify-center w-full">
                    <label for="file-upload" class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 border-dashed hover:border-blue-500 transition-colors">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                            <p class="text-xs text-gray-400">CSV or TXT files up to 20MB</p>
                        </div>
                        <input id="file-upload" name="file" type="file" accept=".csv, .txt" class="hidden" onchange="document.getElementById('file-name-display').innerText = this.files[0]?.name || ''" />
                    </label>
                </div>
                
                <div class="mt-3 flex items-center justify-between">
                    <span id="file-name-display" class="text-sm font-medium text-gray-600"></span>
                    <flux:button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Upload & Profile Data
                    </flux:button>
                </div>
            </form>
        </div>

        <!-- Datasets Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Your Datasets</h3>
            </div>

            @if($datasets->isEmpty())
                <div class="p-8 text-center text-gray-500">
                    No datasets uploaded yet. Upload a CSV file above to begin analysis!
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rows</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detected Columns</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($datasets as $dataset)
                                <tr>
                                    <!-- File Name -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $dataset->name }}
                                    </td>

                                    <!-- Row Count -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($dataset->row_count) }}
                                    </td>

                                    <!-- Column Schema Pills -->
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs">
                                        <div class="flex flex-wrap gap-1">
                                            @if(!empty($dataset->schema_json['columns']))
                                                @foreach(array_slice($dataset->schema_json['columns'], 0, 4) as $column)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $column['name'] ?? $column }}
                                                    </span>
                                                @endforeach
                                                @if(count($dataset->schema_json['columns']) > 4)
                                                    <span class="text-xs text-gray-400 self-center">+{{ count($dataset->schema_json['columns']) - 4 }} more</span>
                                                @endif
                                            @else
                                                <span class="text-xs text-gray-400 italic">No schema parsed</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Uploaded Date -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $dataset->created_at->diffForHumans() }}
                                    </td>

                                    <!-- Action Buttons -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <!-- Start Analysis Chat -->
                                        <form action="{{ route('conversations.store', $dataset) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200">
                                                Start Chat
                                            </button>
                                        </form>

                                        <!-- Delete Dataset -->
                                        <form action="{{ route('datasets.destroy', $dataset) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this dataset?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</x-app-layout>