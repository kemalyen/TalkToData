<div class="flex flex-col h-[600px] bg-white rounded-lg shadow p-4">
    <!-- Message Thread -->
    <div class="flex-1 overflow-y-auto space-y-4 mb-4">
        @foreach($messages as $msg)
            <div class="p-3 rounded-lg {{ $msg->role === 'user' ? 'bg-blue-50 text-right' : 'bg-gray-100 text-left' }}">
                <p class="font-bold text-xs text-gray-500">{{ ucfirst($msg->role) }}</p>
                <div class="mt-1">{!! nl2br(e($msg->content)) !!}</div>

                <!-- If message has a chart payload, render Canvas -->
                @if($msg->chart_payload)
                    <div class="mt-4 max-w-lg mx-auto">
                        <canvas x-data x-init="
                            new Chart($el, {
                                type: '{{ $msg->chart_payload['type'] ?? 'bar' }}',
                                data: {{ json_encode($msg->chart_payload['data']) }}
                            });
                        "></canvas>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Input Form -->
    <form wire:submit.prevent="sendMessage" class="flex gap-2">
        <input type="text" wire:model="prompt" placeholder="Ask a question about your data..." class="flex-1 border rounded px-3 py-2">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
            Send
        </button>
    </form>
</div>