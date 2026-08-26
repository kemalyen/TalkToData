<div x-data="{ dragging: false, fileDrop: false }"
    x-on:dragenter.prevent="dragging = true; fileDrop = true"
    x-on:dragleave.prevent="dragging = false; fileDrop = false"
    x-on:dragover.prevent="dragging = true; fileDrop = true"
    x-on:drop.prevent="dragging = false; fileDrop = false"
    wire:loading.class="opacity-50"
>
    <div class="mb-4 text-center">
        <h3 class="text-sm font-medium text-emerald-700 dark:text-emerald-400">Upload Dataset</h3>
        <p class="text-xs text-zinc-500 dark:text-zinc-400">CSV or TXT files up to 20MB</p>
    </div>

    <div
        class="relative flex min-h-[140px] cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed transition-colors"
        :class="fileDrop ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/20' : 'border-zinc-300 dark:border-zinc-600'"
    >
        <label for="file-input" class="flex flex-col items-center justify-center gap-2 cursor-pointer">
            <svg class="h-8 w-8 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                @if($fileName)
                    {{ $fileName }}
                @else
                    Click to upload or drag & drop
                @endif
            </span>
        </label>
        <input id="file-input" type="file" wire:model="file" accept=".csv,.txt" class="hidden" />
    </div>

    @error('file')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror

    <button type="button"
        wire:click="upload"
        wire:loading.attr="disabled"
        class="mt-3 w-full rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
        data-test="upload-button">
        <span wire:loading.remove wire:target="upload">Upload & Profile Data</span>
        <span wire:loading wire:target="upload">Processing...</span>
    </button>
</div>
