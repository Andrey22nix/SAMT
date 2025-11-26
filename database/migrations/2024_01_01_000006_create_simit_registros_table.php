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
        Schema::create('simit_registros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('placa');
            $table->decimal('valor', 15, 2);
            $table->string('infracciones');
            $table->string('departamento');
            $table->date('fecha');
            $table->string('comparendo');
            $table->enum('estado_pago', ['pagado', 'pendiente', 'vencido'])->default('pendiente');
            $table->string('secretaria');
            $table->string('codigo_infraccion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simit_registros');
    }
};

