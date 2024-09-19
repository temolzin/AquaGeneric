<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LocalitiesTable extends Migration
{
    
    public function up()
    {
        Schema::create('localities', function (Blueprint $table) {
            $table->id()->nullable();
            $table->string('locality_name');
            $table->string('municipality');
            $table->string('state');
            $table->string('zip_code', 5);
        });
    }

    public function down()
    {
        Schema::dropIfExists('localities');
    }
}
