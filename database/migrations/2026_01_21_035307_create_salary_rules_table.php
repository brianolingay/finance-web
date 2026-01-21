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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_rules');
    }
};
