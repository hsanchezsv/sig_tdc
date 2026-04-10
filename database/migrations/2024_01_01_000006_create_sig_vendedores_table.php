<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('sig_vendedores')) {
            Schema::create('sig_vendedores', function (Blueprint $table) {
                $table->increments('id_vendedor');
                $table->string('codigo_vendedor', 30);
                $table->string('nombre_vendedor', 150);
                $table->date('fecha_ingreso')->nullable();
                $table->string('numero_documento', 30)->nullable();
                $table->unsignedInteger('id_sucursal');
                $table->foreign('id_sucursal')->references('id_sucursal')->on('sig_sucursales');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('sig_vendedores');
    }
};
