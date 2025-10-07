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
        Schema::table('trip_expenses', function (Blueprint $table) {
            if (Schema::hasColumn('trip_expenses', 'date') && !Schema::hasColumn('trip_expenses', 'expense_date')) {
                $table->renameColumn('date', 'expense_date');
            }

            if (!Schema::hasColumn('trip_expenses', 'currency')) {
                $table->string('currency', 3)->after('amount');
            }

            if (!Schema::hasColumn('trip_expenses', 'receipt_url')) {
                $table->string('receipt_url')->nullable()->after('expense_date');
            }

            // Indexes
            $table->index('trip_id');
            $table->index('expense_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_expenses', function (Blueprint $table) {
            // Drop added columns if they exist
            if (Schema::hasColumn('trip_expenses', 'receipt_url')) {
                $table->dropColumn('receipt_url');
            }
            if (Schema::hasColumn('trip_expenses', 'currency')) {
                $table->dropColumn('currency');
            }

            // Rename expense_date back to date if appropriate
            if (Schema::hasColumn('trip_expenses', 'expense_date') && !Schema::hasColumn('trip_expenses', 'date')) {
                $table->renameColumn('expense_date', 'date');
            }

            // Drop indexes (names follow Laravel convention)
            $table->dropIndex(['trip_id']);
            $table->dropIndex(['expense_date']);
        });
    }
};


