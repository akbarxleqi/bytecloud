<x-layouts.bytecloud title="Bytecloud Dashboard">
    <section class="p-4 lg:p-8" x-data="{}">
        <div class="mb-8 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-slateText">Dashboard</h1>
                <p class="mt-1 text-sm text-muted">Ringkasan cloud drive pribadi berbasis Telegram Cloud.</p>
            </div>
        </div>

        <div class="mb-8 grid gap-4 md:grid-cols-3">
            @foreach ($stats as $stat)
                <article class="rounded-xl border border-line bg-panel p-5 shadow-sm">
                    <div class="mb-4 flex items-start justify-between">
                        <div class="rounded-xl bg-tint p-3 text-primary shadow-sm"><span class="material-symbols-outlined">{{ $stat['icon'] }}</span></div>
                        <span class="rounded-full bg-tint px-3 py-1 text-[10px] font-bold text-primary uppercase tracking-wider border border-primary/10">{{ $stat['meta'] }}</span>
                    </div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-muted">{{ $stat['label'] }}</div>
                    <div class="mt-1 text-4xl font-bold text-slateText">{{ $stat['value'] }}</div>
                </article>
            @endforeach
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <section class="overflow-hidden rounded-xl border border-line bg-panel xl:col-span-2">
                <div class="flex items-center justify-between border-b border-line p-5">
                    <h2 class="text-lg font-semibold">Recent Files</h2>
                    <a href="/files" class="text-sm font-semibold text-primary">View all</a>
                </div>
                <div class="divide-y divide-line">
                    @foreach ($files as $file)
                        <div class="grid grid-cols-[auto,1fr,auto] items-center gap-4 p-4 hover:bg-soft transition group">
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-soft text-primary group-hover:bg-panel transition">
                                <span class="material-symbols-outlined">{{ $file['icon'] }}</span>
                            </div>
                            <div class="min-w-0">
                                <div class="truncate font-semibold">{{ $file['name'] }}</div>
                                <div class="text-sm text-muted">{{ $file['folder'] }} · {{ $file['size'] }}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="$dispatch('open-preview', { url: '{{ route('drive.files.preview', $file['id']) }}', name: '{{ $file['name'] }}', type: '{{ $file['mime_type'] }}' })" 
                                        class="hidden group-hover:flex h-8 w-8 items-center justify-center rounded-lg hover:bg-panel hover:text-primary transition" title="Preview">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                </button>
                                <a href="{{ route('drive.files.download', $file['id']) }}" class="hidden group-hover:flex h-8 w-8 items-center justify-center rounded-lg hover:bg-panel hover:text-primary transition" title="Download">
                                    <span class="material-symbols-outlined text-[18px]">download</span>
                                </a>
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">{{ $file['status'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <aside class="rounded-xl border border-line bg-panel p-5">
                <h2 class="mb-5 text-lg font-semibold">Telegram Storage</h2>
                <div class="mb-5 rounded-lg bg-soft p-4">
                    <div class="mb-2 flex justify-between text-sm font-semibold">
                        <span>Saved Messages</span><span>45%</span>
                    </div>
                    <div class="h-3 overflow-hidden rounded-full bg-line">
                        <div class="h-full w-[45%] bg-primary shadow-sm"></div>
                    </div>
                </div>
                <div class="space-y-3 text-sm text-muted">
                    <div class="flex justify-between"><span>Provider</span><strong class="text-slateText">Telegram</strong></div>
                    <div class="flex justify-between"><span>Mode</span><strong class="text-slateText">Saved Messages</strong></div>
                    <div class="flex justify-between"><span>Queue</span><strong class="text-slateText">Database</strong></div>
                </div>
            </aside>
        </div>
    </section>
</x-layouts.bytecloud>
