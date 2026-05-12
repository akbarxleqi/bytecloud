<x-layouts.bytecloud title="Bytecloud Upload Queue">
    <section class="p-4 lg:p-8" x-data="{ 
        init() {
            setInterval(() => {
                window.location.reload();
            }, 5000); // Poll every 5 seconds for now
        }
    }">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Upload Queue</h1>
                <p class="mt-1 text-sm text-muted">Managing transfers via Laravel Queue and MadelineProto service stubs.</p>
            </div>
            <div class="flex gap-3">
                <button class="inline-flex items-center gap-2 rounded-lg border border-line bg-panel px-4 py-2 text-sm font-semibold text-muted">
                    <span class="material-symbols-outlined text-[18px]">pause</span> Pause All
                </button>
                <form action="{{ route('drive.queue.clear') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-line bg-panel px-4 py-2 text-sm font-semibold text-rose-600 hover:bg-rose-50 transition">
                        <span class="material-symbols-outlined text-[18px]">delete_sweep</span> Clear Failed
                    </button>
                </form>
            </div>
        </div>

        <div class="space-y-4">
            @foreach ($uploads as $upload)
                <article class="rounded-xl border border-line bg-panel p-5 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-tint text-primary">
                            <span class="material-symbols-outlined text-[28px]">{{ $upload['icon'] }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="mb-2 flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold">{{ $upload['name'] }}</p>
                                    <p class="text-xs text-muted">{{ $upload['size'] }} · Telegram Cloud</p>
                                </div>
                                <div class="text-right">
                                    <span class="mb-1 inline-block rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $upload['status'] === 'failed' ? 'bg-red-100 text-red-700' : ($upload['status'] === 'uploading' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-700') }}">
                                        {{ $upload['status'] }}
                                    </span>
                                    <p class="text-sm font-semibold text-primary">{{ $upload['progress'] }}%</p>
                                    <p class="text-xs text-muted">{{ $upload['speed'] }}</p>
                                </div>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-soft">
                                <div class="h-full {{ $upload['status'] === 'failed' ? 'bg-red-500' : 'bg-gradient-to-r from-telegram to-primary' }}" style="width: {{ $upload['progress'] }}%"></div>
                            </div>
                            @if($upload['status'] === 'failed')
                                <div class="mt-3 p-2 bg-red-50 rounded-lg text-xs text-red-700 border border-red-100 italic">
                                    Error: {{ $upload['error'] ?? 'Unknown error occurred.' }}
                                </div>
                                <div class="flex items-center gap-3 mt-3">
                                    <form action="{{ route('drive.files.retry', $upload['id']) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-xs font-bold text-primary hover:underline flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[14px]">replay</span> Retry Upload
                                        </button>
                                    </form>
                                    <span class="text-muted text-[10px]">|</span>
                                    <form action="{{ route('drive.files.destroy', $upload['id']) }}" method="POST" onsubmit="return confirm('Hapus file ini dari queue?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-bold text-rose-600 hover:underline flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[14px]">delete</span> Remove
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
</x-layouts.bytecloud>
