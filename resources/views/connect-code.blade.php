<x-layouts.bytecloud title="Verify Code" :hideNav="true">
    <div class="w-full max-w-md animate-fade-in p-4">
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-2xl bg-primary text-white shadow-lg">
                <span class="material-symbols-outlined text-[48px]">lock_open</span>
            </div>
            <h1 class="text-3xl font-bold text-slateText">Verify Code</h1>
            <p class="mt-2 text-muted">Enter the 5-digit code from your Telegram app.</p>
        </div>

        <article class="rounded-2xl bg-white p-8 shadow-xl border border-line/50">
            <form action="{{ route('telegram.code.verify') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-slateText mb-2 text-center">Verification Code</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-muted text-[20px]">pin</span>
                        <input type="text" name="code" placeholder="12345" required autofocus
                               class="w-full rounded-xl border border-line bg-surface py-4 pl-12 pr-4 text-center text-xl font-bold tracking-[0.5em] focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                    </div>
                    @error('code')
                        <p class="mt-2 text-xs text-red-600 font-medium text-center">{{ $message }}</p>
                    @enderror
                </div>
                
                <button type="submit" class="w-full rounded-xl bg-primary py-4 text-sm font-bold text-white shadow-lift hover:opacity-90 transition-all active:scale-[0.98]">
                    Verify and Continue
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <a href="{{ route('telegram.show') }}" class="text-sm font-bold text-primary hover:underline">
                    Wrong phone number?
                </a>
            </div>
        </article>
    </div>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
    </style>
</x-layouts.bytecloud>
