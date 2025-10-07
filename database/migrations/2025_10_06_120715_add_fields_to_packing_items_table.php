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
        Schema::table('packing_items', function (Blueprint $table) {
            $table->integer('order')->default(0)->after('is_custom');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade')->after('order');
            $table->enum('category', ['clothing', 'toiletries', 'electronics', 'documents', 'miscellaneous'])->change();
            $table->index(['trip_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packing_items', function (Blueprint $table) {
            $table->dropIndex(['trip_id', 'category']);
            $table->dropForeign(['created_by']);
            $table->dropColumn(['order', 'created_by']);
            $table->string('category')->change();
        });
    }
};
