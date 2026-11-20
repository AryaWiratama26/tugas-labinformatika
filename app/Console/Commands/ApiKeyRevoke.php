<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use Illuminate\Console\Command;

class ApiKeyRevoke extends Command
{
    protected $signature = 'api:key-revoke {key : ID atau prefix key}';
    protected $description = 'Cabut (revoke) API key berdasarkan ID atau prefix';

    public function handle(): int
    {
        $input = $this->argument('key');
        $apiKey = is_numeric($input)
            ? ApiKey::find($input)
            : ApiKey::where('prefix', 'like', $input.'%')->first();

        if (! $apiKey) {
            $this->error('API key tidak ditemukan.');
            return self::FAILURE;
        }

        $apiKey->forceFill(['revoked_at' => now()])->save();
        $this->info("API key '{$apiKey->name}' (ID {$apiKey->id}) sudah dicabut.");
        return self::SUCCESS;
    }
}
