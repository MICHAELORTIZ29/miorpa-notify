<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'payment_acknowledgements',
            function (Blueprint $table) {
                $table->id();

                $publicId = $table->char('public_id', 26);

                if (
                    Schema::getConnection()->getDriverName() === 'mysql'
                ) {
                    $publicId
                        ->charset('ascii')
                        ->collation('ascii_bin');
                }

                $table->foreignId('payment_id')
                    ->constrained('payments')
                    ->cascadeOnDelete();

                $table->foreignId('user_id')
                    ->constrained('users')
                    ->restrictOnDelete();

                $table->foreignId('receiver_device_id')
                    ->nullable()
                    ->constrained('devices')
                    ->nullOnDelete();

                $table->timestamp('viewed_at')->nullable();
                $table->timestamp('confirmed_at')->nullable();

                $table->timestamps();

                $table->unique('public_id');

                $table->unique(
                    ['payment_id', 'user_id'],
                    'payment_acknowledgements_payment_user_unique'
                );

                $table->index('viewed_at');
                $table->index('confirmed_at');
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_acknowledgements');
    }
};