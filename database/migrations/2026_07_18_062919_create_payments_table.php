<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $publicId = $table->char('public_id', 26);

            if (Schema::getConnection()->getDriverName() === 'mysql') {
                $publicId->charset('ascii')->collation('ascii_bin');
            }

            $table->foreignId('business_id')
                ->constrained('businesses')
                ->restrictOnDelete();

            $table->foreignId('payment_provider_id')
                ->constrained('payment_providers')
                ->restrictOnDelete();

            $table->foreignId('emitter_device_id')
                ->constrained('devices')
                ->restrictOnDelete();

            /*
             * Hash determinista del evento generado por la APK.
             * Impide guardar dos veces la misma notificación.
             */
            $table->char('source_event_hash', 64);

            $table->string('external_reference', 120)->nullable();
            $table->string('payer_name', 190)->nullable();
            $table->string('payer_document', 30)->nullable();

            // Nunca utilizar float o double para dinero.
            $table->decimal('amount', 14, 2);
            $table->char('currency', 3)->default('PEN');

            $table->string('status', 30)->default('received');
            $table->string('parser_version', 40)->nullable();

            $table->timestamp('occurred_at', 3);
            $table->timestamp('received_at', 3);

            /*
             * Se almacenará cifrado mediante el modelo de Laravel.
             * Contiene la información técnica original necesaria
             * para diagnóstico, no se muestra al cajero.
             */
            $table->longText('raw_payload')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->unique('public_id');

            $table->unique(
                ['business_id', 'source_event_hash'],
                'payments_business_source_event_unique'
            );

            $table->index(
                ['business_id', 'occurred_at'],
                'payments_business_occurred_index'
            );

            $table->index(
                ['business_id', 'status'],
                'payments_business_status_index'
            );

            $table->index(
                ['payment_provider_id', 'occurred_at'],
                'payments_provider_occurred_index'
            );

            $table->index('emitter_device_id');
            $table->index('amount');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};