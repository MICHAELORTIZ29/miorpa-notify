<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('status', 20)->default('active');
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
        });

        Schema::create('plan_limits', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('plan_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('limit_code', 100);
            $table->decimal('numeric_value', 18, 4)->nullable();
            $table->boolean('boolean_value')->nullable();
            $table->string('text_value')->nullable();
            $table->timestamps();

            $table->unique([
                'plan_id',
                'limit_code',
            ]);
        });

        Schema::create('subscriptions', function (Blueprint $table): void {
            $table->id();
            $table->char('public_id', 26)->unique();

            $table->foreignId('business_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('plan_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('billing_cycle', 20);
            $table->string('status', 20)->default('active');

            $table->decimal('price', 18, 2)->nullable();
            $table->char('currency', 3)->default('PEN');

            $table->dateTime('starts_at', 6);
            $table->dateTime('current_period_ends_at', 6);
            $table->dateTime('grace_ends_at', 6)->nullable();
            $table->dateTime('suspended_at', 6)->nullable();
            $table->dateTime('ended_at', 6)->nullable();

            $table->boolean('auto_suspend')->default(true);
            $table->json('terms_snapshot_json')->nullable();

            $table->timestamps();

            $table->index([
                'business_id',
                'status',
            ]);

            $table->index([
                'status',
                'current_period_ends_at',
            ]);
        });

        Schema::create(
            'subscription_limit_overrides',
            function (Blueprint $table): void {
                $table->id();

                $table->foreignId('subscription_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->string('limit_code', 100);
                $table->decimal('numeric_value', 18, 4);
                $table->timestamps();

                $table->unique([
                    'subscription_id',
                    'limit_code',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_limit_overrides');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plan_limits');
        Schema::dropIfExists('plans');
    }
};