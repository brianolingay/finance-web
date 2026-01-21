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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained();
            $table->string('name');
            $table->string('sku')->nullable();
            $table->text('description')->nullable();
            $table->bigInteger('unit_cost_cents')->nullable();
            $table->bigInteger('unit_price_cents')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['account_id', 'sku']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
