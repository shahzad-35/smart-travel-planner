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
        Schema::table('collection_destinations', function (Blueprint $table) {
            $table->json('destination_data')->nullable();
            $table->dropColumn(['destination_name', 'country_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collection_destinations', function (Blueprint $table) {
            $table->string('destination_name');
            $table->string('country_code', 2);
            $table->dropColumn('destination_data');
        });
    }
};
