<x-layouts.bytecloud title="Shared File">
    <section class="flex min-h-[calc(100vh-4rem)] items-center justify-center p-4">
        <article class="w-full max-w-lg rounded-xl border border-line bg-panel p-8 text-center shadow-lift">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-tint text-primary">
                <span class="material-symbols-outlined text-[32px]">share</span>
            </div>
            <h1 class="text-2xl font-bold">{{ $share->file->original_name }}</h1>
            <p class="mt-2 text-sm text-muted">{{ $share->file->human_size }} · {{ $share->file->mime_type ?? 'unknown type' }}</p>
            <a href="{{ route('drive.share.download', $share->token) }}" class="mt-6 inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-3 text-sm font-semibold text-white">
                <span class="material-symbols-outlined text-[18px]">download</span>
                Download
            </a>
        </article>
    </section>
</x-layouts.bytecloud>
