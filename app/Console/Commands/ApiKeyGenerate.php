<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use Illuminate\Console\Command;

class ApiKeyGenerate extends Command
{
    protected $signature = 'api:key-generate {--name= : Nama pemilik key} {--expires= : Tanggal kedaluwarsa (Y-m-d)}';
    protected $description = 'Buat API key baru (plaintext hanya ditampilkan sekali)';

    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Nama pemilik key', 'SIMBA-FT');
        $generated = ApiKey::generatePlaintext();

        $key = ApiKey::create([
            'name' => $name,
            'key_hash' => $generated['hash'],
            'prefix' => $generated['prefix'],
            'expires_at' => $this->option('expires') ?: null,
        ]);

        $this->info("API key dibuat untuk: {$key->name} (ID {$key->id}, prefix {$key->prefix})");
        $this->warn('Simpan key ini baik-baik, tidak akan ditampilkan lagi:');
        $this->line($generated['plain']);

        return self::SUCCESS;
    }
}
