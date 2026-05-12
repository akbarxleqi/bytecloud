<div
    x-data="{ 
        show: false, 
        name: '{{ $name }}',
        @if($attributes->has('id')) id: '{{ $attributes->get('id') }}' @endif
    }"
    x-show="show"
    x-on:open-modal.window="if($event.detail === name) show = true"
    x-on:close-modal.window="if($event.detail === name) show = false"
    x-on:keydown.escape.window="show = false"
    style="display: none;"
    class="fixed inset-0 z-50 overflow-y-auto"
>
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slateText/50 backdrop-blur-sm transition-opacity"
            @click="show = false"
        ></div>

        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative transform overflow-hidden rounded-2xl bg-panel p-6 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
        >
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-xl font-bold text-slateText">{{ $title }}</h3>
                <button @click="show = false" class="text-muted hover:text-slateText">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            {{ $slot }}
        </div>
    </div>
</div>
