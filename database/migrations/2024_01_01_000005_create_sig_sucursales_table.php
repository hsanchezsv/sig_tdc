<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('sig_sucursales')) {
            Schema::create('sig_sucursales', function (Blueprint $table) {
                $table->increments('id_sucursal');
                $table->string('codigo', 20);
                $table->string('nombre', 100);
                $table->unsignedInteger('id_pais');
                $table->foreign('id_pais')->references('id_pais')->on('sig_paises');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('sig_sucursales');
    }
};
