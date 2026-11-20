<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use Illuminate\Console\Command;

class ApiKeyList extends Command
{
    protected $signature = 'api:key-list';
    protected $description = 'Tampilkan daftar API key (tanpa plaintext)';

    public function handle(): int
    {
        $keys = ApiKey::latest()->get(['id', 'name', 'prefix', 'last_used_at', 'revoked_at', 'expires_at', 'created_at']);
        $this->table(
            ['ID', 'Nama', 'Prefix', 'Terakhir dipakai', 'Status', 'Kedaluwarsa', 'Dibuat'],
            $keys->map(fn ($k) => [
                $k->id, $k->name, $k->prefix,
                $k->last_used_at?->format('d M Y H:i') ?? '-',
                $k->revoked_at ? 'dicabut' : ($k->isExpired() ? 'kedaluwarsa' : 'aktif'),
                $k->expires_at?->format('d M Y') ?? '-',
                $k->created_at->format('d M Y H:i'),
            ])->toArray()
        );
        return self::SUCCESS;
    }
}
