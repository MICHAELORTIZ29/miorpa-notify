<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();

            $publicId = $table->char('public_id', 26);

            if (Schema::getConnection()->getDriverName() === 'mysql') {
                $publicId->charset('ascii')->collation('ascii_bin');
            }

            $table->foreignId('business_id')
                ->constrained('businesses')
                ->restrictOnDelete();

            $table->foreignId('authorized_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('name', 120);

            // emitter: Android que detecta pagos.
            // receiver: navegador o futura aplicación receptora.
            $table->string('type', 30);

            $table->string('platform', 30);
            $table->string('status', 30)->default('active');

            // Solo se almacena el hash SHA-256 del token.
            $table->char('token_hash', 64)->nullable()->unique();

            // Identificador de instalación anonimizado.
            $table->char('installation_hash', 64)->nullable();

            $table->string('app_version', 40)->nullable();
            $table->string('last_ip', 45)->nullable();
            $table->string('user_agent', 500)->nullable();

            $table->json('capabilities')->nullable();

            $table->timestamp('authorized_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('disabled_at')->nullable();
            $table->timestamp('revoked_at')->nullable();

            $table->timestamps();

            $table->unique(
                ['business_id', 'installation_hash'],
                'devices_business_installation_unique'
            );

            $table->index(
                ['business_id', 'status'],
                'devices_business_status_index'
            );

            $table->index(
                ['business_id', 'type'],
                'devices_business_type_index'
            );

            $table->index('last_seen_at');
            $table->unique('public_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};