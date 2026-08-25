<x-layouts::app :title="__('Conversation with ' . $conversation->dataset->name)">
    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mb-4">
            Conversation: {{ $conversation->title }}
        </h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-6">
            Dataset: {{ $conversation->dataset->name }} | Status: {{ ucfirst($conversation->status) }}
        </p>
    </div>
    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-xl font-semibold text-zinc-800 dark:text-zinc-200 mb-4">
            Analyzing: {{ $conversation->dataset->name }}
        </h2>

        <!-- Render Reactive Livewire Chat -->
        <livewire:chat-bot :conversation="$conversation" />
    </div>
</x-layouts::app>
