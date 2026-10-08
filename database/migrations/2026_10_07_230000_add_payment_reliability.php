<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->json('diagnostics')->nullable();
            $table->timestamp('diagnostics_received_at')->nullable();
        });
        Schema::create('payment_push_outbox', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('state', 30)->default('pending');
            $table->unsignedInteger('attempts')->default(0);
            $table->json('delivered_subscription_ids')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamp('next_attempt_at')->nullable();
            $table->timestamp('lease_until')->nullable();
            $table->string('lease_token', 36)->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            $table->index(['state', 'next_attempt_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_push_outbox');
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['diagnostics', 'diagnostics_received_at']);
        });
    }
};
