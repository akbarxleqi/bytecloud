<?php

namespace App\Services\Telegram;

use App\Models\TelegramAccount;
use danog\MadelineProto\API;
use danog\MadelineProto\Settings;
use Illuminate\Support\Facades\File;
use RuntimeException;

class TelegramAuthService
{
    public function startLogin(TelegramAccount $account): array
    {
        $this->assertReady($account);

        $api = $this->api($account);
        $result = $api->phoneLogin($this->normalizePhone($account->phone_number));

        $this->mark($account, API::WAITING_CODE, [
            'auth' => $this->safeAuthPayload($result),
        ]);

        return $result;
    }

    public function submitCode(TelegramAccount $account, string $code): array
    {
        $api = $this->api($account);
        $result = $api->completePhoneLogin($code);

        $this->syncAuthorizationState($account, $api, $result);

        return $result;
    }

    public function submitPassword(TelegramAccount $account, string $password): array
    {
        $api = $this->api($account);
        $result = $api->complete2faLogin($password);

        $this->syncAuthorizationState($account, $api, $result);

        return $result;
    }

    public function test(TelegramAccount $account): array|false
    {
        $api = $this->api($account);
        $self = $api->getSelf();

        $this->syncAuthorizationState($account, $api, is_array($self) ? ['user' => $self] : []);

        return $self;
    }

    public function disconnect(TelegramAccount $account): void
    {
        try {
            $this->api($account)->logout();
        } catch (\Throwable) {
            // If Telegram refuses or the local session is already invalid, still clear local state.
        }

        $account->update([
            'is_connected' => false,
            'last_connected_at' => null,
            'meta' => array_merge($account->meta ?? [], [
                'auth_state' => API::LOGGED_OUT,
                'auth_label' => 'logged_out',
            ]),
        ]);
    }

    public function api(TelegramAccount $account): API
    {
        $sessionPath = $this->sessionPath($account);
        File::ensureDirectoryExists(dirname($sessionPath));

        if ($account->session_path !== $sessionPath) {
            $account->forceFill(['session_path' => $sessionPath])->save();
        }

        $settings = new Settings;
        $settings->getAppInfo()
            ->setApiId((int) $account->api_id)
            ->setApiHash($account->api_hash);

        return new API($sessionPath, $settings);
    }

    public function apiFresh(TelegramAccount $account): API
    {
        // For background jobs, we want a fresh instance that can handle its own IPC
        return $this->api($account);
    }

    private function assertReady(TelegramAccount $account): void
    {
        foreach (['api_id', 'api_hash', 'phone_number', 'session_name'] as $field) {
            if (blank($account->{$field})) {
                throw new RuntimeException("Field {$field} wajib diisi sebelum login Telegram.");
            }
        }
    }

    private function sessionPath(TelegramAccount $account): string
    {
        $name = preg_replace('/[^a-zA-Z0-9_.-]/', '-', $account->session_name ?: 'default');

        return storage_path('app/'.trim(config('drive.telegram_session_path'), '/').'/'.$name);
    }

    private function normalizePhone(?string $phone): string
    {
        $phone = trim((string) $phone);

        return str_starts_with($phone, '+') ? $phone : '+'.$phone;
    }

    private function syncAuthorizationState(TelegramAccount $account, API $api, array $result): void
    {
        $state = $api->getAuthorization();
        $connected = $state === API::LOGGED_IN;

        $this->mark($account, $state, [
            'auth' => $this->safeAuthPayload($result),
        ], $connected);
    }

    private function mark(TelegramAccount $account, int $state, array $meta = [], bool $connected = false): void
    {
        $account->update([
            'is_connected' => $connected,
            'last_connected_at' => $connected ? now() : $account->last_connected_at,
            'meta' => array_merge($account->meta ?? [], [
                'auth_state' => $state,
                'auth_label' => $this->stateLabel($state),
            ], $meta),
        ]);
    }

    private function stateLabel(int $state): string
    {
        return match ($state) {
            API::WAITING_CODE => 'waiting_code',
            API::WAITING_PASSWORD => 'waiting_password',
            API::LOGGED_IN => 'logged_in',
            API::LOGGED_OUT => 'logged_out',
            API::WAITING_SIGNUP => 'waiting_signup',
            default => 'not_logged_in',
        };
    }

    public function syncProfile(TelegramAccount $account): void
    {
        $api = $this->api($account);
        $self = $api->getSelf();

        $name = trim(($self['first_name'] ?? '') . ' ' . ($self['last_name'] ?? ''));
        $username = $self['username'] ?? null;
        $photoUrl = null;

        try {
            if (isset($self['photo']) && $self['photo']['_'] !== 'userProfilePhotoEmpty') {
                $storagePath = 'profile-photos/' . $account->id . '.jpg';
                $fullPath = storage_path('app/public/' . $storagePath);
                
                File::ensureDirectoryExists(dirname($fullPath));
                
                $api->downloadToFile($self, $fullPath);
                $photoUrl = '/storage/' . $storagePath;
            }
        } catch (\Throwable $e) {
            // Log error for debugging if needed
        }

        $account->update([
            'name' => $name ?: ($username ?: $account->phone_number),
            'meta' => array_merge($account->meta ?? [], [
                'user_id' => $self['id'],
                'username' => $username,
                'photo_url' => $photoUrl,
            ]),
        ]);
    }

    private function safeAuthPayload(array $payload): array
    {
        unset($payload['phone_code_hash']);

        return $payload;
    }
}
