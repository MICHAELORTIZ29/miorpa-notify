<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();

            $table->char('public_id', 26)
                ->charset('ascii')
                ->collation('ascii_bin')
                ->unique();

            $table->string('name', 150);
            $table->string('legal_name', 200)->nullable();
            $table->string('tax_id', 30)->nullable();
            $table->string('timezone', 64)->default('America/Lima');
            $table->string('status', 20)->default('trial');

            $table->string('contact_name', 150)->nullable();
            $table->string('contact_email', 254)->nullable();
            $table->string('contact_phone', 30)->nullable();

            $table->dateTime('suspended_at', 6)->nullable();
            $table->dateTime('closed_at', 6)->nullable();

            $table->timestamps(6);

            $table->index('status');
            $table->index('created_at');
            $table->index('tax_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};