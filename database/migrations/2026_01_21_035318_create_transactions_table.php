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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained();
            $table->string('direction');
            $table->bigInteger('amount_cents');
            $table->string('currency', 3);
            $table->string('source_type');
            $table->unsignedBigInteger('source_id');
            $table->dateTime('occurred_at');
            $table->timestamps();

            $table->index(['account_id', 'occurred_at']);
            $table->index(['source_type', 'source_id']);
            $table->index(['account_id', 'direction']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
