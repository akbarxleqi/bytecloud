<x-layouts.bytecloud title="Bytecloud Files">
    <section class="p-4 lg:p-8">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <nav class="flex items-center gap-2 text-sm text-muted">
                <a href="{{ route('drive.files.index') }}" class="flex items-center gap-1 hover:text-primary transition">
                    <span class="material-symbols-outlined text-[18px]">home</span>
                    <span>Home</span>
                </a>
                @foreach($breadcrumbs as $breadcrumb)
                    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                    <a href="{{ route('drive.files.index', ['folder_id' => $breadcrumb['id']]) }}" class="hover:text-primary transition {{ $loop->last ? 'font-bold text-slateText' : '' }}">
                        {{ $breadcrumb['name'] }}
                    </a>
                @endforeach
                @if(empty($breadcrumbs))
                    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                    <strong class="text-slateText">All Files</strong>
                @endif
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
            <h1 class="mb-4 text-lg font-bold text-slateText">Folders</h1>
            <div class="mb-10 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($folders as $folder)
                    <a href="{{ route('drive.files.index', ['folder_id' => $folder['id']]) }}" class="rounded-xl border border-line bg-panel p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lift {{ request('folder_id') == $folder['id'] ? 'ring-2 ring-primary' : '' }}">
                        <div class="mb-3 flex items-start justify-between">
                            <span class="material-symbols-outlined text-[40px] text-primary" style="font-variation-settings: 'FILL' 1;">folder</span>
                            <span class="material-symbols-outlined text-muted">more_vert</span>
                        </div>
                        <h2 class="truncate font-semibold">{{ $folder['name'] }}</h2>
                        <p class="mt-1 text-sm text-muted">{{ $folder['items'] }} items · {{ $folder['size'] }}</p>
                    </a>
                @endforeach
            </div>
        @endif

        <section class="overflow-hidden rounded-xl border border-line bg-panel">
            <div class="flex items-center justify-between border-b border-line p-5">
                <h2 class="text-lg font-semibold">Files</h2>
                <span class="text-sm text-muted">{{ $files->total() }} records</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead class="bg-soft text-xs font-semibold uppercase tracking-wide text-muted">
                    <tr>
                        <th class="px-5 py-3">Name</th>
                        <th class="px-5 py-3">Folder</th>
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
                            <td class="px-5 py-4 text-muted">{{ $file['folder'] }}</td>
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
                                    <a href="{{ route('drive.files.preview', $file['id']) }}" class="rounded-lg p-1 hover:bg-soft hover:text-primary transition" title="Preview" target="_blank">
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    </a>
                                    <a href="{{ route('drive.files.download', $file['id']) }}" class="rounded-lg p-1 hover:bg-soft hover:text-primary transition" title="Download">
                                        <span class="material-symbols-outlined text-[20px]">download</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-muted">
                                No files found.
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
