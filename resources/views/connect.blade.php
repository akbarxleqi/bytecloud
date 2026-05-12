<x-layouts.bytecloud title="Connect Telegram" :hideNav="true">
    <div class="w-full max-w-md animate-fade-in p-4">
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-2xl bg-primary text-white shadow-lg">
                <span class="material-symbols-outlined text-[48px]">send</span>
            </div>
            <h1 class="text-3xl font-bold text-slateText">Connect Telegram</h1>
            <p class="mt-2 text-muted">Enter your credentials to link your account.</p>
        </div>

        <article class="rounded-2xl bg-white p-8 shadow-xl border border-line/50">
            <form action="{{ route('telegram.connect') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-slateText mb-2">Telegram API ID</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-muted text-[20px]">key</span>
                        <input type="text" name="api_id" placeholder="123456" required
                               class="w-full rounded-xl border border-line bg-surface py-3.5 pl-12 pr-4 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all font-medium">
                    </div>
                    @error('api_id')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slateText mb-2">Telegram API Hash</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-muted text-[20px]">password</span>
                        <input type="text" name="api_hash" placeholder="abcdef123456..." required
                               class="w-full rounded-xl border border-line bg-surface py-3.5 pl-12 pr-4 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all font-medium">
                    </div>
                    @error('api_hash')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slateText mb-2">Phone Number</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-muted text-[20px]">phone</span>
                        <input type="text" name="phone" placeholder="+628123456789" required
                               class="w-full rounded-xl border border-line bg-surface py-3.5 pl-12 pr-4 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all font-medium">
                    </div>
                    @error('phone')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
                
                <button type="submit" class="w-full rounded-xl bg-primary py-4 text-sm font-bold text-white shadow-lift hover:opacity-90 transition-all active:scale-[0.98] mt-2">
                    Send Verification Code
                </button>
            </form>
            
            <div class="mt-8 pt-6 border-t border-line/30">
                <p class="text-center text-[11px] text-muted font-medium uppercase tracking-widest leading-relaxed">
                    Powered by <span class="text-primary">Bytecloud</span> & Telegram MTProto
                </p>
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
