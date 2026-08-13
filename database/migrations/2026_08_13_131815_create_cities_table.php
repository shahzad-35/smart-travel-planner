<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('state_name');
            $table->string('country_code', 2);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedBigInteger('population')->default(0);

            $table->index(['country_code', 'state_name']);
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
