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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained();
            $table->foreignId('category_id')->nullable()->constrained();
            $table->string('description')->nullable();
            $table->string('status')->nullable();
            $table->bigInteger('amount_cents');
            $table->string('currency', 3);
            $table->dateTime('occurred_at');
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();

            $table->index(['account_id', 'occurred_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
