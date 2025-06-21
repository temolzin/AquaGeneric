<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('locality_id');
            $table->unsignedBigInteger('created_by');
            $table->string('name');
            $table->date('start_date');
            $table->string('description');
            $table->unsignedBigInteger('category_id');
            $table->string('status');
            $table->timestamps();

            $table->foreign('locality_id')->references('id')->on('localities')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('incident_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incidents');
    }
}
