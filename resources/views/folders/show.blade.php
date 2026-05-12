<x-layouts.bytecloud title="{{ $folder->name }} - Bytecloud">
    <section class="p-4 lg:p-8" x-data="{}">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <nav class="flex items-center gap-2 text-sm text-muted">
                <a href="{{ route('drive.folders.index') }}" class="flex items-center gap-1 hover:text-primary transition">
                    <span class="material-symbols-outlined text-[18px]">home</span>
                    <span>All Folders</span>
                </a>
                @foreach($breadcrumbs as $breadcrumb)
                    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                    <a href="{{ route('drive.folders.show', $breadcrumb['id']) }}" class="hover:text-primary transition {{ $loop->last ? 'font-bold text-slateText' : '' }}">
                        {{ $breadcrumb['name'] }}
                    </a>
                @endforeach
            </nav>
            <div class="flex gap-3" x-data>
                <button @click="$dispatch('open-modal', 'new-folder')" class="inline-flex items-center gap-2 rounded-xl border border-line bg-panel px-4 py-2 text-sm font-bold text-muted hover:bg-soft transition shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">add_box</span>
                    New Folder
                </button>
                <button @click="$dispatch('open-modal', 'upload-file')" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-sm font-bold text-white shadow-lift hover:opacity-90 transition">
                    <span class="material-symbols-outlined text-[18px]">cloud_upload</span>
                    Upload File
                </button>
            </div>
        </div>

        @if($folders->isNotEmpty())
            <h2 class="mb-4 text-sm font-bold uppercase tracking-wider text-muted">Sub-Folders</h2>
            <div class="mb-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($folders as $subfolder)
                    <a href="{{ route('drive.folders.show', $subfolder['id']) }}" class="flex items-center gap-4 rounded-xl border border-line bg-panel p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lift">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-tint text-primary">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">folder</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="truncate font-semibold text-slateText">{{ $subfolder['name'] }}</h3>
                            <p class="text-xs text-muted">{{ $subfolder['items'] }} files</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        <section class="overflow-hidden rounded-xl border border-line bg-panel shadow-sm">
            <div class="flex items-center justify-between border-b border-line p-5">
                <h2 class="text-lg font-semibold">Files in {{ $folder->name }}</h2>
                <span class="text-sm text-muted">{{ $files->total() }} records</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead class="bg-soft text-xs font-semibold uppercase tracking-wide text-muted">
                    <tr>
                        <th class="px-5 py-3">Name</th>
                        <th class="px-5 py-3">Type</th>
                        <th class="px-5 py-3">Size</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                    @forelse ($files as $file)
                        <tr>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-soft text-primary">
                                        <span class="material-symbols-outlined">{{ $file['icon'] }}</span>
                                    </div>
                                    <span class="font-semibold">{{ $file['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-muted">{{ $file['type'] }}</td>
                            <td class="px-5 py-4 text-muted">{{ $file['size'] }}</td>
                            <td class="px-5 py-4">
                                @php
                                    $statusColor = match($file['status']) {
                                        'uploaded' => 'bg-emerald-50 text-emerald-700',
                                        'failed' => 'bg-rose-50 text-rose-700',
                                        'pending', 'uploading' => 'bg-amber-50 text-amber-700',
                                        default => 'bg-slate-50 text-slate-700'
                                    };
                                @endphp
                                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $statusColor }}">
                                    {{ ucfirst($file['status']) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right text-muted">
                                <div class="flex justify-end gap-2">
                                    <button @click="$dispatch('open-preview', { url: '{{ route('drive.files.preview', $file['id']) }}', name: '{{ $file['name'] }}', type: '{{ $file['mime_type'] }}' })" 
                                            class="rounded-lg p-1 hover:bg-soft hover:text-primary transition" title="Preview">
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    </button>
                                    <a href="{{ route('drive.files.download', $file['id']) }}" class="rounded-lg p-1 hover:bg-soft hover:text-primary transition" title="Download">
                                        <span class="material-symbols-outlined text-[20px]">download</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-muted">
                                This folder is empty.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($files->hasPages())
                <div class="border-t border-line p-5">
                    {{ $files->links() }}
                </div>
            @endif
        </section>
    </section>
</x-layouts.bytecloud>
