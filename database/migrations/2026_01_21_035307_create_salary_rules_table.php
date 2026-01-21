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
        Schema::create('salary_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('account_id')->constrained();
            $table->string('name');
            $table->string('type', 16);
            $table->bigInteger('fixed_cents')->nullable();
            $table->integer('commission_bps')->nullable();
            $table->string('currency', 3)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement(
                "ALTER TABLE salary_rules ADD CONSTRAINT salary_rules_type_check CHECK (type IN ('fixed', 'commission', 'hybrid'))"
            );
            DB::statement(
                "ALTER TABLE salary_rules ADD CONSTRAINT salary_rules_fixed_amount_check CHECK ((type NOT IN ('fixed', 'hybrid')) OR fixed_cents IS NOT NULL)"
            );
            DB::statement(
                "ALTER TABLE salary_rules ADD CONSTRAINT salary_rules_commission_bps_check CHECK ((type NOT IN ('commission', 'hybrid')) OR commission_bps IS NOT NULL)"
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_rules');
    }
};
