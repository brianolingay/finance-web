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
        Schema::create('cashier_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained();
            $table->foreignId('cashier_id')->constrained();
            $table->uuid('salary_rule_id')->nullable();
            $table->unsignedBigInteger('amount_cents');
            $table->string('currency', 3);
            $table->dateTime('paid_at');
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('salary_rule_id')->references('id')->on('salary_rules');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_salaries');
    }
};
