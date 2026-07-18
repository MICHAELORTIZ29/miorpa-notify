<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->char('public_id', 26)
                ->charset('ascii')
                ->collation('ascii_bin')
                ->unique()
                ->after('id');

            $table->foreignId('business_id')
                ->nullable()
                ->after('public_id')
                ->constrained('businesses')
                ->restrictOnDelete();

            $table->string('role_code', 30)
                ->default('cashier')
                ->after('business_id');

            $table->string('phone', 30)
                ->nullable()
                ->after('email');

            $table->string('status', 20)
                ->default('active')
                ->after('password');

            $table->dateTime('last_login_at', 6)->nullable();
            $table->dateTime('password_changed_at', 6)->nullable();
            $table->dateTime('disabled_at', 6)->nullable();

            $table->foreignId('created_by_user_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->index(['business_id', 'role_code', 'status']);
            $table->index(['business_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['created_by_user_id']);
            $table->dropForeign(['business_id']);

            $table->dropIndex(['business_id', 'role_code', 'status']);
            $table->dropIndex(['business_id', 'name']);

            $table->dropColumn([
                'public_id',
                'business_id',
                'role_code',
                'phone',
                'status',
                'last_login_at',
                'password_changed_at',
                'disabled_at',
                'created_by_user_id',
            ]);
        });
    }
};