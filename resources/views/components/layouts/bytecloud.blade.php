<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Bytecloud' }}</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#24a1de',
                        telegram: '#24a1de',
                        surface: '#f4f7f9',
                        slateText: '#001e2e',
                        muted: '#707579',
                        line: '#dfe3e7',
                        soft: '#ffffff',
                        panel: '#ffffff',
                        tint: '#e1f5fe',
                    },
                    fontFamily: { sans: ['Geist', 'sans-serif'] },
                    boxShadow: { lift: '0 4px 12px rgba(15, 23, 42, 0.04)' },
                },
            },
        };
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen bg-surface font-sans text-slateText" 
      x-data="{ 
        previewOpen: false, 
        previewUrl: '', 
        previewName: '',
        previewType: '',
        uploadFolderId: 0
      }" 
      @open-preview.window="
        previewUrl = $event.detail.url; 
        previewName = $event.detail.name; 
        previewType = $event.detail.type;
        previewOpen = true;
      ">

    <!-- Global Preview Modal -->
    <div x-show="previewOpen" 
         x-cloak 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slateText/90 backdrop-blur-sm"
         @keydown.escape.window="previewOpen = false">
        
        <div class="relative max-w-5xl w-full max-h-[90vh] flex flex-col items-center" @click.away="previewOpen = false">
            <!-- Header -->
            <div class="absolute -top-12 left-0 right-0 flex items-center justify-between text-white">
                <h3 class="font-bold truncate pr-8" x-text="previewName"></h3>
                <button @click="previewOpen = false" class="hover:text-primary transition">
                    <span class="material-symbols-outlined text-[32px]">close</span>
                </button>
            </div>

            <!-- Content -->
            <div class="w-full h-full flex items-center justify-center bg-panel/5 rounded-2xl overflow-hidden shadow-2xl border border-white/10">
                <template x-if="previewType.startsWith('image/')">
                    <img :src="previewUrl" class="max-w-full max-h-[80vh] object-contain select-none">
                </template>
                <template x-if="previewType === 'application/pdf'">
                    <iframe :src="previewUrl" class="w-full h-[80vh] rounded-xl border-0"></iframe>
                </template>
                <template x-if="previewType.startsWith('video/')">
                    <video :src="previewUrl" controls class="max-w-full max-h-[80vh] rounded-xl"></video>
                </template>
                <template x-if="!previewType.startsWith('image/') && previewType !== 'application/pdf' && !previewType.startsWith('video/')">
                    <div class="p-20 text-center text-white">
                        <span class="material-symbols-outlined text-[64px] mb-4">description</span>
                        <p>Preview not available for this file type.</p>
                        <a :href="previewUrl" download class="mt-6 inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-3 font-bold text-white">
                            <span class="material-symbols-outlined">download</span> Download to View
                        </a>
                    </div>
                </template>
            </div>
        </div>
    </div>
@if(!isset($hideNav))
<aside class="fixed left-0 top-0 z-40 hidden h-full w-[260px] flex-col border-r border-line bg-surface py-8 lg:flex">
    <div class="mb-8 px-6">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-white shadow-sm">
                <span class="material-symbols-outlined">cloud_done</span>
            </div>
            <div>
                <div class="text-2xl font-bold text-slateText">Bytecloud</div>
                <div class="text-xs font-medium text-muted">Personal Storage</div>
            </div>
        </div>
    </div>
    <div class="mb-6 px-4 space-y-2" x-data>
        <button @click="$dispatch('open-modal', 'new-folder')" class="flex w-full items-center justify-center gap-2 rounded-xl bg-soft px-4 py-3 text-sm font-semibold text-primary transition hover:bg-tint border border-primary/10 shadow-sm">
            <span class="material-symbols-outlined text-[20px]">add_box</span>
            New Folder
        </button>
        <button @click="$dispatch('open-modal', 'upload-file')" class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-4 py-3 text-sm font-semibold text-white transition hover:opacity-90 shadow-lift">
            <span class="material-symbols-outlined text-[20px]">cloud_upload</span>
            Upload File
        </button>
    </div>
    <nav class="flex flex-1 flex-col gap-1 px-2">
        @foreach ([
            ['/', 'dashboard', 'Dashboard'],
            ['/folders', 'folder', 'All Folders'],
            ['/files', 'folder_open', 'All Files'],
            ['/queue', 'cloud_upload', 'Upload Queue'],
        ] as [$href, $icon, $label])
            <a href="{{ $href }}" class="{{ request()->path() === trim($href, '/') || (request()->path() === '/' && $href === '/') ? 'border-l-4 border-primary bg-tint text-primary' : 'text-muted hover:bg-soft' }} flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold transition">
                <span class="material-symbols-outlined">{{ $icon }}</span>
                {{ $label }}
            </a>
        @endforeach
    </nav>
    <div class="px-6">
        <div class="rounded-2xl border border-line bg-soft p-4 shadow-sm">
            <div class="mb-2 flex items-center justify-between text-xs font-bold text-muted">
                <span>Storage</span><span class="text-primary">{{ $storagePercentage }}%</span>
            </div>
            <div class="h-2 overflow-hidden rounded-full bg-line">
                <div class="h-full rounded-full bg-primary" style="width: {{ $storagePercentage }}%"></div>
            </div>
            <div class="mt-2 text-[10px] font-bold text-muted uppercase tracking-wider">{{ $storageUsed }} / Unlimited</div>
        </div>
    </div>
</aside>

<main class="min-h-screen lg:ml-[260px]">
    <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-line bg-surface px-4 lg:px-8">
        <form action="{{ route('drive.files.index') }}" method="GET" class="relative w-full max-w-md">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-muted text-[20px]">search</span>
            <input name="search" value="{{ request('search') }}" class="w-full rounded-full border-0 bg-line/20 py-2 pl-10 pr-4 text-sm focus:ring-2 focus:ring-primary focus:bg-white transition-all" placeholder="Search files, folders..." type="search">
        </form>
        <div class="ml-4 flex items-center gap-3">
            <div class="hidden text-sm font-bold text-slateText sm:block">{{ $telegramAccount?->name ?? 'User' }}</div>
            @if($telegramAccount?->meta['photo_url'] ?? false)
                <img src="{{ $telegramAccount->meta['photo_url'] }}" alt="Profile" class="h-8 w-8 rounded-full border border-primary/10 object-cover">
            @else
                <div class="h-8 w-8 rounded-full bg-tint flex items-center justify-center text-primary font-bold text-xs border border-primary/10">
                    {{ strtoupper(substr($telegramAccount?->name ?? 'U', 0, 1)) }}
                </div>
            @endif
        </div>
    </header>
    {{ $slot }}
</main>
@else
<main class="min-h-screen flex items-center justify-center bg-[#f0f2f5]">
    {{ $slot }}
</main>
@endif

<x-modal name="new-folder" title="Create New Folder">
    <form action="{{ route('drive.folders.store') }}" method="POST">
        @csrf
        <input type="hidden" name="parent_id" value="{{ $folder->id ?? request('folder_id') }}">
        <div class="mb-6">
            <label class="block text-sm font-semibold text-slateText mb-2">Folder Name</label>
            <input type="text" name="name" required placeholder="Project Assets"
                   class="w-full rounded-xl border border-line bg-soft py-3 px-4 text-sm focus:ring-2 focus:ring-primary">
        </div>
        <div class="flex justify-end gap-3">
            <button type="button" @click="$dispatch('close-modal', 'new-folder')" class="px-4 py-2 text-sm font-semibold text-muted hover:text-slateText transition">Cancel</button>
            <button type="submit" class="rounded-lg bg-primary px-6 py-2 text-sm font-bold text-white hover:bg-[#004c6d] transition">Create Folder</button>
        </div>
    </form>
</x-modal>

<x-modal name="upload-file" title="Upload File">
    <form action="{{ route('drive.files.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="folder_id" x-bind:value="uploadFolderId || ''">
        <div class="mb-6">
            <label class="block text-sm font-semibold text-slateText mb-2">Select File</label>
            <div class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-line bg-soft p-10 transition hover:border-primary group">
                <span class="material-symbols-outlined text-[48px] text-muted group-hover:text-primary mb-2 transition">cloud_upload</span>
                <p class="text-sm text-muted group-hover:text-slateText transition">Click to select or drag and drop</p>
                <input type="file" name="file" required class="absolute inset-0 cursor-pointer opacity-0" onchange="this.parentElement.querySelector('p').innerText = this.files[0].name">
            </div>
            <p class="mt-2 text-xs text-muted text-center">Max file size: {{ config('drive.max_upload_mb') }} MB</p>
        </div>
        <div class="flex justify-end gap-3">
            <button type="button" @click="$dispatch('close-modal', 'upload-file')" class="px-4 py-2 text-sm font-semibold text-muted hover:text-slateText transition">Cancel</button>
            <button type="submit" class="rounded-lg bg-primary px-6 py-2 text-sm font-bold text-white hover:bg-[#004c6d] transition">Start Upload</button>
        </div>
    </form>
</x-modal>
</body>
</html>
