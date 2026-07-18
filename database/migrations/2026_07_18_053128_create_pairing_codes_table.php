<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pairing_codes', function (Blueprint $table) {
            $table->id();

            $publicId = $table->char('public_id', 26);

            if (Schema::getConnection()->getDriverName() === 'mysql') {
                $publicId->charset('ascii')->collation('ascii_bin');
            }

            $table->foreignId('business_id')
                ->constrained('businesses')
                ->restrictOnDelete();

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('used_by_device_id')
                ->nullable()
                ->constrained('devices')
                ->nullOnDelete();

            // Hash del código completo MNP-XXXX-XXXX.
            $table->char('code_hash', 64)->unique();

            // Solo sirve para identificarlo visualmente en auditoría.
            $table->string('code_suffix', 4);

            $table->string('device_type', 30);
            $table->unsignedTinyInteger('max_uses')->default(1);
            $table->unsignedTinyInteger('uses_count')->default(0);

            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamp('revoked_at')->nullable();

            $table->timestamps();

            $table->unique('public_id');

            $table->index(
                ['business_id', 'expires_at'],
                'pairing_codes_business_expiration_index'
            );

            $table->index(
                ['business_id', 'device_type'],
                'pairing_codes_business_type_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pairing_codes');
    }
};