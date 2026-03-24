<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('sig_proveedores')) {
            Schema::create('sig_proveedores', function (Blueprint $table) {
                $table->increments('id_proveedor');
                $table->string('nombre_proveedor', 150);
                $table->unsignedInteger('id_pais');
                $table->string('direccion', 200)->nullable();
                $table->string('telefono', 20)->nullable();
                $table->string('nombre_contacto', 100)->nullable();
                $table->foreign('id_pais')->references('id_pais')->on('sig_paises');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('sig_proveedores');
    }
};
