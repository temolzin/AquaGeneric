<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocalitiesTable extends Migration
{
    public function up()
    {
        Schema::create('localities', function (Blueprint $table) {
            $table->id();
            $table->string('locality_name');
            $table->string('municipality');
            $table->string('state');
            $table->string('zip_code', 5);
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('localities');
    }
}
