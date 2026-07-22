<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'businesses',
            function (Blueprint $table): void {
                $table->string(
                    'suspension_reason',
                    30
                )
                    ->nullable()
                    ->after('suspended_at');

                $table->index(
                    'suspension_reason',
                    'businesses_suspension_reason_index'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'businesses',
            function (Blueprint $table): void {
                $table->dropIndex(
                    'businesses_suspension_reason_index'
                );

                $table->dropColumn(
                    'suspension_reason'
                );
            }
        );
    }
};