<x-app-layout>
    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            Analyzing: {{ $conversation->dataset->name }}
        </h2>
        
        <!-- Render Reactive Livewire Chat -->
        <livewire:chat-bot :conversation="$conversation" />
    </div>

    <!-- Include Chart.js for Rendering Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</x-app-layout>