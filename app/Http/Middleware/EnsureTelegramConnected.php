<?php

namespace App\Http\Middleware;

use App\Models\TelegramAccount;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTelegramConnected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $account = TelegramAccount::first();

        if (! $account || ! $account->is_connected) {
            return redirect()->route('telegram.show');
        }

        return $next($request);
    }
}
