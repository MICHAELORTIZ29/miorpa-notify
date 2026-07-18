<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_providers', function (Blueprint $table) {
            $table->id();

            $publicId = $table->char('public_id', 26);
            $code = $table->string('code', 40);

            if (Schema::getConnection()->getDriverName() === 'mysql') {
                $publicId->charset('ascii')->collation('ascii_bin');
                $code->charset('ascii')->collation('ascii_bin');
            }

            $table->string('name', 100);

            // Permite varias aplicaciones oficiales por proveedor.
            $table->json('android_packages')->nullable();

            $table->string('status', 30)->default('active');
            $table->json('configuration')->nullable();

            $table->timestamps();

            $table->unique('public_id');
            $table->unique('code');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_providers');
    }
};