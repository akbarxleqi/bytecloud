<x-layouts.bytecloud title="Two-Step Verification" :hideNav="true">
    <div class="w-full max-w-md animate-fade-in p-4">
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-2xl bg-primary text-white shadow-lg">
                <span class="material-symbols-outlined text-[48px]">security</span>
            </div>
            <h1 class="text-3xl font-bold text-slateText">2FA Required</h1>
            <p class="mt-2 text-muted">Please enter your Two-Step Verification password.</p>
        </div>

        <article class="rounded-2xl bg-white p-8 shadow-xl border border-line/50">
            <form action="{{ route('telegram.password.verify') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-slateText mb-2">Password</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-muted text-[20px]">key</span>
                        <input type="password" name="password" required autofocus
                               class="w-full rounded-xl border border-line bg-surface py-4 pl-12 pr-4 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all font-medium">
                    </div>
                    @error('password')
                        <p class="mt-2 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
                
                <button type="submit" class="w-full rounded-xl bg-primary py-4 text-sm font-bold text-white shadow-lift hover:opacity-90 transition-all active:scale-[0.98]">
                    Unlock Account
                </button>
            </form>
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
