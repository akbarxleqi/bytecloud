<x-layouts.bytecloud title="Sign In" :hideNav="true">
    <div class="w-full max-w-md animate-fade-in p-4">
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-2xl bg-primary text-white shadow-lg">
                <span class="material-symbols-outlined text-[48px]">cloud_done</span>
            </div>
            <h1 class="text-3xl font-bold text-slateText">Welcome to Bytecloud</h1>
            <p class="mt-2 text-muted">Sign in to access your personal storage.</p>
        </div>

        <article class="rounded-2xl bg-white p-8 shadow-xl border border-line/50">
            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf
                
                <div>
                    <label class="block text-sm font-bold text-slateText mb-2">Email Address</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-muted text-[20px]">mail</span>
                        <input type="email" name="email" placeholder="you@example.com" value="{{ old('email') }}" required autofocus
                               class="w-full rounded-xl border border-line bg-surface py-3.5 pl-12 pr-4 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all font-medium">
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-bold text-slateText">Password</label>
                    </div>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-muted text-[20px]">lock</span>
                        <input type="password" name="password" placeholder="••••••••" required
                               class="w-full rounded-xl border border-line bg-surface py-3.5 pl-12 pr-4 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all font-medium">
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="rounded border-line text-primary focus:ring-primary h-4 w-4">
                        <span class="ml-2 text-xs font-semibold text-muted">Remember me</span>
                    </label>
                </div>

                <button type="submit" class="w-full rounded-xl bg-primary py-4 text-sm font-bold text-white shadow-lift hover:opacity-90 transition-all active:scale-[0.98] mt-2">
                    Sign In
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-sm text-muted">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="font-bold text-primary hover:underline">Register here</a>
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
