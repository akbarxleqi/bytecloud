<?php

namespace Database\Seeders;

use App\Models\DriveFile;
use App\Models\DriveFolder;
use App\Models\DriveTag;
use App\Models\TelegramAccount;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate([
            'email' => env('ADMIN_EMAIL', 'akbardev47@gmail.com'),
        ], [
            'name' => 'Bytecloud Admin',
            'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
        ]);

        $telegram = TelegramAccount::query()->firstOrCreate([
            'session_name' => 'default',
        ], [
            'name' => 'Saved Messages',
            'api_id' => '000000',
            'api_hash' => 'replace-me',
            'default_chat_type' => 'saved_messages',
            'is_connected' => false,
        ]);

        $folders = collect(['Documents', 'Projects', 'Photos', 'Backups'])
            ->mapWithKeys(fn (string $name) => [
                $name => DriveFolder::query()->firstOrCreate([
                    'slug' => Str::slug($name),
                    'parent_id' => null,
                ], [
                    'name' => $name,
                    'color' => '#24a1de',
                ]),
            ]);

        $tag = DriveTag::query()->firstOrCreate([
            'slug' => 'sample',
        ], [
            'name' => 'Sample',
            'color' => '#24a1de',
        ]);

        collect([
            ['telegram-drive-spec.pdf', 'Documents', 'application/pdf', 2936012, 'pdf'],
            ['dashboard-preview.png', 'Projects', 'image/png', 1468006, 'png'],
            ['archive-backup.zip', 'Backups', 'application/zip', 6657199308, 'zip'],
            ['meeting-notes.txt', 'Documents', 'text/plain', 43008, 'txt'],
        ])->each(function (array $item) use ($folders, $telegram, $tag): void {
            [$name, $folder, $mime, $size, $extension] = $item;

            $file = DriveFile::query()->firstOrCreate([
                'original_name' => $name,
            ], [
                'folder_id' => $folders[$folder]->id,
                'telegram_account_id' => $telegram->id,
                'extension' => $extension,
                'mime_type' => $mime,
                'size_bytes' => $size,
                'status' => 'uploaded',
                'visibility' => 'private',
                'uploaded_at' => now()->subDays(random_int(1, 12)),
            ]);

            $file->tags()->syncWithoutDetaching($tag->id);
        });
    }
}
