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
        Schema::table('trips', function (Blueprint $table) {
            $table->enum('type', ['business', 'leisure', 'adventure', 'family', 'solo'])->change();
            $table->enum('status', ['planned', 'ongoing', 'completed', 'cancelled'])->default('planned')->change();

            $table->string('country_code', 2)->after('destination');
            $table->text('notes')->nullable()->after('status');
            $table->json('metadata')->nullable()->after('notes');

            $table->index('user_id');
            $table->index('start_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->string('type')->change();
            $table->string('status')->default('planned')->change();

            $table->dropColumn(['country_code', 'notes', 'metadata']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['start_date']);
            $table->dropIndex(['status']);
        });
    }
};
