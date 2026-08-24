<div class="flex h-[680px] flex-col overflow-hidden rounded-lg bg-white shadow">
    <div class="flex-1 space-y-4 overflow-y-auto p-4 sm:p-6">
        @forelse($messages as $msg)
        @php($analysis = $msg->role === 'assistant' && is_array($msg->chart_payload) ? $msg->chart_payload : [])
        <article class="rounded-lg border p-4 {{ $msg->role === 'user' ? 'ml-8 border-blue-100 bg-blue-50' : 'mr-8 border-gray-200 bg-gray-50' }}">
            <p class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ ucfirst($msg->role) }}</p>
            <div class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-800">{{ $msg->content }}</div>

            @if($analysis)
            <div class="mt-5 space-y-5">
                <section>
                    <h3 class="text-sm font-semibold text-gray-800">Dataset metrics</h3>
                    <div class="mt-2 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded border border-gray-200 bg-white p-3">
                            <p class="text-xs text-gray-500">Rows</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ number_format($analysis['row_count'] ?? 0) }}</p>
                        </div>
                        <div class="rounded border border-gray-200 bg-white p-3">
                            <p class="text-xs text-gray-500">Columns</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ count($analysis['columns'] ?? []) }}</p>
                        </div>
                        <div class="rounded border border-gray-200 bg-white p-3">
                            <p class="text-xs text-gray-500">Numeric fields</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ count($analysis['numeric_summary'] ?? []) }}</p>
                        </div>
                        <div class="rounded border border-gray-200 bg-white p-3">
                            <p class="text-xs text-gray-500">Sample rows</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ count($analysis['result_sample'] ?? []) }}</p>
                        </div>
                    </div>
                </section>

                @if($analysis['numeric_summary'] ?? [])
                <section>
                    <h3 class="text-sm font-semibold text-gray-800">Numerical fields</h3>
                    <div class="mt-2 overflow-x-auto rounded border border-gray-200 bg-white">
                        <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                                <tr>
                                    <th class="px-3 py-2">Field</th>
                                    <th class="px-3 py-2">Min</th>
                                    <th class="px-3 py-2">Max</th>
                                    <th class="px-3 py-2">Mean</th>
                                    <th class="px-3 py-2">Sum</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($analysis['numeric_summary'] as $field => $metrics)
                                <tr>
                                    <td class="whitespace-nowrap px-3 py-2 font-medium text-gray-800">{{ $field }}</td>
                                    <td class="px-3 py-2">{{ $metrics['min'] ?? 'n/a' }}</td>
                                    <td class="px-3 py-2">{{ $metrics['max'] ?? 'n/a' }}</td>
                                    <td class="px-3 py-2">{{ $metrics['mean'] ?? 'n/a' }}</td>
                                    <td class="px-3 py-2">{{ $metrics['sum'] ?? 'n/a' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
                @endif

                @if($analysis['result_sample'] ?? [])
                <section>
                    <h3 class="text-sm font-semibold text-gray-800">Sample data</h3>
                    <div class="mt-2 overflow-x-auto rounded border border-gray-200 bg-white">
                        <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                                <tr>
                                    @foreach(array_keys($analysis['result_sample'][0]) as $field)
                                    <th class="whitespace-nowrap px-3 py-2">{{ $field }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($analysis['result_sample'] as $row)
                                <tr>
                                    @foreach($row as $value)
                                    <td class="whitespace-nowrap px-3 py-2 text-gray-700">{{ is_array($value) ? json_encode($value) : ($value ?? 'null') }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
                @endif

                @if($analysis['chart_config']['data'] ?? null)
                <section wire:ignore>
                    <h3 class="text-sm font-semibold text-gray-800">Visualization</h3>
                    <div class="relative mt-2 h-64 rounded border border-gray-200 bg-white p-3">
                        <canvas x-data x-init="new Chart($el, @js($analysis['chart_config']))"></canvas>
                    </div>
                </section>
                @endif
            </div>
            @endif
        </article>
        @empty
        <div class="flex h-full items-center justify-center text-center text-sm text-gray-500">
            Ask a question to begin analyzing this dataset.
        </div>
        @endforelse

        <div wire:loading wire:target="sendMessage" class="mr-8 rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gray-500">
            Analyzing your dataset...
        </div>
    </div>

    <div class="border-t border-gray-200 p-4 sm:p-6">
        @error('prompt')
        <p class="mb-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
        <form wire:submit="sendMessage" class="flex gap-2">
            <input type="text" wire:model="prompt" placeholder="Ask a question about your data..." class="min-w-0 flex-1 rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" wire:loading.attr="disabled" wire:target="sendMessage">
            <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50" wire:loading.attr="disabled" wire:target="sendMessage">
                <span wire:loading.remove wire:target="sendMessage">Send</span>
                <span wire:loading wire:target="sendMessage">Working...</span>
            </button>
        </form>
    </div>
</div>