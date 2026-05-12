<x-layouts.bytecloud title="All Folders - Bytecloud">
    <section class="p-4 lg:p-8">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <nav class="flex items-center gap-2 text-sm text-muted">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-1 hover:text-primary transition">
                    <span class="material-symbols-outlined text-[18px]">home</span>
                    <span>Home</span>
                </a>
                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                <strong class="text-slateText">All Folders</strong>
            </nav>
            <div class="flex gap-3" x-data>
                <button @click="$dispatch('open-modal', 'new-folder')" class="inline-flex items-center gap-2 rounded-xl border border-line bg-panel px-4 py-2 text-sm font-bold text-muted hover:bg-soft transition shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">add_box</span>
                    New Folder
                </button>
            </div>
        </div>

        @if($folders->isNotEmpty())
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($folders as $folder)
                    <a href="{{ route('drive.folders.show', $folder['id']) }}" class="group relative rounded-2xl border border-line bg-panel p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lift">
                        <div class="mb-4 flex items-start justify-between">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-tint text-primary group-hover:bg-primary group-hover:text-white transition">
                                <span class="material-symbols-outlined text-[32px]" style="font-variation-settings: 'FILL' 1;">folder</span>
                            </div>
                            <button class="rounded-lg p-1 text-muted hover:bg-soft hover:text-primary transition">
                                <span class="material-symbols-outlined text-[20px]">more_vert</span>
                            </button>
                        </div>
                        <h2 class="truncate text-lg font-bold text-slateText">{{ $folder['name'] }}</h2>
                        <div class="mt-2 flex items-center gap-2 text-sm text-muted">
                            <span class="font-medium">{{ $folder['items'] }} files</span>
                            <span class="h-1 w-1 rounded-full bg-line"></span>
                            <span>{{ $folder['size'] }}</span>
                        </div>
                        
                        <div class="mt-4 flex -space-x-2">
                            @for($i = 0; $i < min(3, $folder['items']); $i++)
                                <div class="h-6 w-6 rounded-full border-2 border-panel bg-soft flex items-center justify-center text-[10px] font-bold text-primary">
                                    {{ $i + 1 }}
                                </div>
                            @endfor
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-line bg-panel p-20 text-center">
                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-tint text-primary">
                    <span class="material-symbols-outlined text-[40px]">create_new_folder</span>
                </div>
                <h3 class="text-xl font-bold text-slateText">No folders yet</h3>
                <p class="mt-2 max-w-xs text-muted">Organize your cloud files by creating your first folder.</p>
                <button @click="$dispatch('open-modal', 'new-folder')" class="mt-6 rounded-xl bg-primary px-6 py-3 text-sm font-bold text-white shadow-lift hover:opacity-90 transition">
                    Create Folder
                </button>
            </div>
        @endif
    </section>
</x-layouts.bytecloud>
