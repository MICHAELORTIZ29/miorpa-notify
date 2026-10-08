<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_captures', function (Blueprint $t) {
            $t->id();
            $t->foreignId('business_id')->constrained()->cascadeOnDelete();
            $t->foreignId('device_id')->constrained()->cascadeOnDelete();
            $t->string('capture_id', 64);
            $t->string('package_name', 150);
            $t->string('state', 30);
            $t->string('reason', 80)->nullable();
            $t->text('raw_payload');
            $t->timestamp('occurred_at');
            $t->string('event_id', 100)->nullable();
            $t->string('provider_code', 30)->nullable();
            $t->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $t->unsignedInteger('revision')->default(0);
            $t->unsignedInteger('retry_version')->default(0);
            $t->unsignedInteger('applied_retry_version')->default(0);
            $t->timestamp('reviewed_at')->nullable();
            $t->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
            $t->unique(['device_id', 'capture_id']);
            $t->index(['business_id', 'state', 'created_at']);
        });
        Schema::create('device_incidents', function (Blueprint $t) {
            $t->id();
            $t->foreignId('business_id')->constrained()->cascadeOnDelete();
            $t->foreignId('device_id')->constrained()->cascadeOnDelete();
            $t->string('kind', 50);
            $t->string('message', 500);
            $t->timestamp('opened_at');
            $t->timestamp('last_observed_at');
            $t->timestamp('resolved_at')->nullable();
            $t->timestamps();
            $t->index(['device_id', 'kind', 'resolved_at']);
            $t->index(['business_id', 'resolved_at']);
        });
        Schema::create('payment_reconciliations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('business_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('provider_code', 30);
            $t->text('result');
            $t->unsignedInteger('total');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_reconciliations');
        Schema::dropIfExists('device_incidents');
        Schema::dropIfExists('notification_captures');
    }
};
