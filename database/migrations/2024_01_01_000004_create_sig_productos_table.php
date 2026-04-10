<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('sig_productos')) {
            Schema::create('sig_productos', function (Blueprint $table) {
                $table->increments('id_producto');
                $table->string('codigo_producto', 50);
                $table->string('nombre_producto', 150);
                $table->unsignedInteger('id_proveedor');
                $table->decimal('precio_unidad', 10, 2)->nullable();
                $table->unsignedInteger('id_pais');
                $table->date('fecha_compra')->nullable();
                $table->string('lote_numero', 50)->nullable();
                $table->foreign('id_proveedor')->references('id_proveedor')->on('sig_proveedores');
                $table->foreign('id_pais')->references('id_pais')->on('sig_paises');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('sig_productos');
    }
};
