<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('sig_paises')) {
            Schema::create('sig_paises', function (Blueprint $table) {
                $table->increments('id_pais');
                $table->string('nombre', 100);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('sig_paises');
    }
};
