<div class="flex h-[calc(100vh-64px)] gap-6">
    <aside class="w-80 flex flex-col overflow-y-auto">
        <livewire:dataset-upload />

        <div class="mt-4 flex flex-col gap-2">
            <h3 class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Datasets</h3>

            @forelse($datasets as $dataset)
                @php
                    $columnNames = collect($dataset->schema_json['columns'] ?? [])
                        ->map(fn ($col) => $col['name'] ?? $col)
                        ->take(4);
                @endphp
                <div class="group flex items-center justify-between rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-medium text-zinc-800 dark:text-zinc-100">{{ $dataset->name }}</p>
                        <div class="mt-1 flex flex-wrap gap-1">
                            @foreach($columnNames as $column)
                                <span class="inline-block rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">
                                    {{ $column }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex gap-1">
                        <button type="button"
                            wire:click="startChat({{ $dataset->id }})"
                            class="rounded-full bg-emerald-600 px-3 py-1 text-xs font-medium text-white opacity-0 transition-opacity hover:bg-emerald-700 group-hover:opacity-100"
                            data-test="start-chat-button">
                            Chat
                        </button>
                        <form action="{{ route('datasets.destroy', $dataset) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this dataset?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="rounded-full p-1 text-xs text-zinc-500 hover:text-red-600"
                                data-test="delete-dataset-button">
                                &times;
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-sm text-zinc-500 dark:text-zinc-400">No datasets uploaded yet. Upload a CSV file to get started.</p>
            @endforelse
        </div>
    </aside>

    <div class="flex-1 overflow-y-auto">
        @if($activeConversation)
            <livewire:chat-bot :conversation="$activeConversation" :key="$activeConversation->id" />
        @else
            <div class="flex h-full min-h-[400px] flex-col items-center justify-center rounded-xl border border-dashed border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800/50">
                <svg class="h-12 w-12 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12.75A4.75 4.75 0 1114.25 8 4.75 4.75 0 018 12.75zM21 12c0 .81-.2 1.59-.56 2.29l-.4.63a.75.75 0 01-1.06.29L16 14.83a4.75 4.75 0 01-6.94 0l-1.03-.58a.75.75 0 01-.29-1.06l.63-.4c.7-.36 1.48-.56 2.29-.56h.56a4.75 4.75 0 018.38-4.25z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15.5V18a3 3 0 00 3 3h.75a.75.75 0 00 0-1.5H15a1.5 1.5 0 01-1.5-1.5v-2.5H12z"></path>
                </svg>
                <p class="mt-4 text-sm text-zinc-500 dark:text-zinc-400">No dataset selected</p>
                <p class="text-xs text-zinc-400 dark:text-zinc-500">Upload a dataset and click "Chat" to start analyzing your data.</p>
            </div>
        @endif
    </div>
</div>
