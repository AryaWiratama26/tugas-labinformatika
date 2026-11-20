<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    protected $fillable = [
        'name',
        'key_hash',
        'prefix',
        'last_used_at',
        'revoked_at',
        'expires_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'revoked_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && now()->isAfter($this->expires_at);
    }

    public function isValid(): bool
    {
        return ! $this->isRevoked() && ! $this->isExpired();
    }

    public static function hash(string $plainKey): string
    {
        return hash('sha256', $plainKey);
    }

    /**
     * Generate a new plaintext key + attributes for storage.
     * Returns [plain, prefix, hash].
     */
    public static function generatePlaintext(string $prefixHint = 'tl'): array
    {
        $plain = $prefixHint.'_'.Str::random(40);
        return [
            'plain' => $plain,
            'prefix' => substr($plain, 0, 12),
            'hash' => self::hash($plain),
        ];
    }

    public static function findByPlain(string $plainKey): ?self
    {
        return self::where('key_hash', self::hash($plainKey))->first();
    }
}
