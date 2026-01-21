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
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropIndex('inventory_movements_source_type_source_id_index');
            $table->unique([
                'account_id',
                'source_type',
                'source_id',
                'product_id',
                'movement_type',
            ], 'inventory_movements_uidx_account_source_product_mtype');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropUnique('inventory_movements_uidx_account_source_product_mtype');
            $table->index(
                ['source_type', 'source_id'],
                'inventory_movements_source_type_source_id_index',
            );
        });
    }
};
