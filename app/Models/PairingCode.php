<?php

namespace App\Models;

use App\Models\Concerns\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PairingCode extends Model
{
    use HasFactory;
    use HasPublicId;

    protected $fillable = [
        'business_id',
        'created_by',
        'used_by_device_id',
        'code_hash',
        'code_suffix',
        'device_type',
        'max_uses',
        'uses_count',
        'expires_at',
        'used_at',
        'revoked_at',
    ];

    protected $hidden = [
        'code_hash',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function usedByDevice(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'used_by_device_id');
    }

    public function isUsable(): bool
    {
        return $this->revoked_at === null
            && $this->expires_at->isFuture()
            && $this->uses_count < $this->max_uses;
    }

    public static function issue(
        Business $business,
        User $creator,
        string $deviceType,
        int $validMinutes = 10
    ): array {
        $plainCode = self::generatePlainCode();

        $pairingCode = self::query()->create([
            'business_id' => $business->id,
            'created_by' => $creator->id,
            'code_hash' => self::hashPlainCode($plainCode),
            'code_suffix' => substr($plainCode, -4),
            'device_type' => $deviceType,
            'max_uses' => 1,
            'uses_count' => 0,
            'expires_at' => now()->addMinutes($validMinutes),
        ]);

        return [
            'pairing_code' => $pairingCode,
            'plain_code' => $plainCode,
        ];
    }

    public static function hashPlainCode(string $plainCode): string
    {
        $normalized = strtoupper(trim($plainCode));

        return hash('sha256', $normalized);
    }

    private static function generatePlainCode(): string
    {
        // Se excluyen caracteres confusos: 0, O, 1, I.
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        $firstPart = '';
        $secondPart = '';

        for ($position = 0; $position < 4; $position++) {
            $firstPart .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            $secondPart .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        return "MNP-{$firstPart}-{$secondPart}";
    }
}