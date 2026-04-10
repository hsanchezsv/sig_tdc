<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('sig_ventas')) {
            Schema::create('sig_ventas', function (Blueprint $table) {
                $table->increments('id_venta');
                $table->date('fecha_compra');
                $table->string('num_factura', 45);
                $table->unsignedInteger('id_producto');
                $table->unsignedInteger('id_vendedor');
                $table->decimal('monto_facturado', 10, 0);
                $table->unsignedInteger('id_sucursal');
                $table->unsignedInteger('id_promocion')->nullable();
                $table->integer('cantidad');
                $table->foreign('id_producto')->references('id_producto')->on('sig_productos');
                $table->foreign('id_vendedor')->references('id_vendedor')->on('sig_vendedores');
                $table->foreign('id_sucursal')->references('id_sucursal')->on('sig_sucursales');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('sig_ventas');
    }
};
