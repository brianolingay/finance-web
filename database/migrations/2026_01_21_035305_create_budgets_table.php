<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained();
            $table->foreignId('category_id')->nullable()->constrained();
            $table->string('month', 7);
            $table->bigInteger('amount_cents');
            $table->string('currency', 3);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['account_id', 'month']);
            $table->unique(
                ['account_id', 'category_id', 'month'],
                'budgets_account_category_month_unique'
            );
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement(
                'CREATE UNIQUE INDEX budgets_account_month_unique_null_category ON budgets (account_id, month) WHERE category_id IS NULL'
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropUnique('budgets_account_category_month_unique');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS budgets_account_month_unique_null_category');
        }

        Schema::dropIfExists('budgets');
    }
};
