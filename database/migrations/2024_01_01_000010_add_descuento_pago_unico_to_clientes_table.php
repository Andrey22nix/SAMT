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
        if (!Schema::hasTable('clientes')) {
            return;
        }

        Schema::table('clientes', function (Blueprint $table) {
            if (!Schema::hasColumn('clientes', 'descuento_pago_unico')) {
                $table->decimal('descuento_pago_unico', 5, 2)->nullable()->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            if (Schema::hasColumn('clientes', 'descuento_pago_unico')) {
                $table->dropColumn('descuento_pago_unico');
            }
        });
    }
};

