<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('sig_clientes')) {
            Schema::create('sig_clientes', function (Blueprint $table) {
                $table->increments('id_cliente');
                $table->string('nombre_cliente', 150);
                $table->unsignedInteger('id_pais');
                $table->string('nit', 30)->nullable();
                $table->string('direccion', 200)->nullable();
                $table->string('contacto_nombre', 100)->nullable();
                $table->string('telefono', 20)->nullable();
                $table->date('fecha_ingreso')->nullable();
                $table->foreign('id_pais')->references('id_pais')->on('sig_paises');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('sig_clientes');
    }
};
