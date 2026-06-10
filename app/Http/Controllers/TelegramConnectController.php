<?php

namespace App\Http\Controllers;

use App\Models\TelegramAccount;
use App\Services\Telegram\TelegramAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TelegramConnectController extends Controller
{
    public function show()
    {
        $account = auth()->user()->telegramAccount;
        
        if ($account?->is_connected) {
            return redirect()->route('dashboard');
        }

        return view('connect', ['account' => $account]);
    }

    public function connect(Request $request, TelegramAuthService $authService)
    {
        $request->validate([
            'api_id' => 'required|string',
            'api_hash' => 'required|string',
            'phone' => 'required|string',
        ]);

        $user = auth()->user();
        $account = $user->telegramAccount ?? new TelegramAccount();

        $account->fill([
            'user_id' => $user->id,
            'api_id' => $request->api_id,
            'api_hash' => $request->api_hash,
            'phone_number' => $request->phone,
        ]);

        if (!$account->exists) {
            $account->session_name = 'bytecloud_session_'.Str::random(8);
        }

        $account->save();

        try {
            $authService->startLogin($account);
            return redirect()->route('telegram.code.show');
        } catch (\Exception $e) {
            return back()->withErrors(['phone' => $e->getMessage()]);
        }
    }

    public function showCode()
    {
        return view('connect-code');
    }

    public function verifyCode(Request $request, TelegramAuthService $authService)
    {
        $request->validate(['code' => 'required|string']);
        $account = auth()->user()->telegramAccount ?: abort(404);

        try {
            $authService->submitCode($account, $request->code);
            
            if ($account->fresh()->is_connected) {
                $authService->syncProfile($account);
                return redirect()->route('dashboard');
            }

            // Might need password
            if (($account->meta['auth_label'] ?? '') === 'waiting_password') {
                return redirect()->route('telegram.password.show');
            }

            return back()->withErrors(['code' => 'Verification failed.']);
        } catch (\Exception $e) {
            return back()->withErrors(['code' => $e->getMessage()]);
        }
    }

    public function showPassword()
    {
        return view('connect-password');
    }

    public function verifyPassword(Request $request, TelegramAuthService $authService)
    {
        $request->validate(['password' => 'required|string']);
        $account = auth()->user()->telegramAccount ?: abort(404);

        try {
            $authService->submitPassword($account, $request->password);
            $authService->syncProfile($account);
            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            return back()->withErrors(['password' => $e->getMessage()]);
        }
    }
}
