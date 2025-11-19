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
            if (!Schema::hasColumn('clientes', 'forma_pago')) {
                $table->enum('forma_pago', ['pago_unico', 'acuerdo_pago'])->nullable();
            }
            if (!Schema::hasColumn('clientes', 'numero_cuotas')) {
                $table->integer('numero_cuotas')->nullable();
            }
            if (!Schema::hasColumn('clientes', 'porcentaje_primera_cuota')) {
                $table->decimal('porcentaje_primera_cuota', 5, 2)->nullable()->default(30.00);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            if (Schema::hasColumn('clientes', 'porcentaje_primera_cuota')) {
                $table->dropColumn('porcentaje_primera_cuota');
            }
            if (Schema::hasColumn('clientes', 'numero_cuotas')) {
                $table->dropColumn('numero_cuotas');
            }
            if (Schema::hasColumn('clientes', 'forma_pago')) {
                $table->dropColumn('forma_pago');
            }
        });
    }
};

