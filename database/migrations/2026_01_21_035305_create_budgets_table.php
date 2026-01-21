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
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement(
                'ALTER TABLE budgets ADD COLUMN category_key BIGINT GENERATED ALWAYS AS (COALESCE(category_id, 0)) STORED'
            );
            DB::statement(
                'CREATE UNIQUE INDEX budgets_account_category_month_unique ON budgets (account_id, category_key, month)'
            );
        } elseif (DB::getDriverName() === 'mysql') {
            DB::statement(
                'ALTER TABLE budgets ADD COLUMN category_key BIGINT GENERATED ALWAYS AS (COALESCE(category_id, 0)) STORED'
            );
            DB::statement(
                'CREATE UNIQUE INDEX budgets_account_category_month_unique ON budgets (account_id, category_key, month)'
            );
        } elseif (DB::getDriverName() === 'sqlite') {
            DB::statement(
                'ALTER TABLE budgets ADD COLUMN category_key BIGINT GENERATED ALWAYS AS (COALESCE(category_id, 0)) STORED'
            );
            DB::statement(
                'CREATE UNIQUE INDEX budgets_account_category_month_unique ON budgets (account_id, category_key, month)'
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
            DB::statement('ALTER TABLE budgets DROP COLUMN IF EXISTS category_key');
        } elseif (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE budgets DROP COLUMN category_key');
        }

        Schema::dropIfExists('budgets');
    }
};
