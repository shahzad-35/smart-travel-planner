<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('temperature_unit')->default('C'); // C or F
            $table->string('theme')->default('light'); // light, dark, system
            $table->string('timezone')->default('UTC');
            $table->json('default_packing_items')->nullable();
            $table->json('notifications')->nullable(); // {email: bool, in_app: bool}
            $table->json('privacy_settings')->nullable(); // {profile_visibility: string, trip_sharing: string}
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
