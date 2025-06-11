<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('locality_id');
            $table->unsignedBigInteger('created_by');
            $table->string('name');
            $table->string('last_name');
            $table->string('locality');
            $table->string('zip_code', 5);
            $table->string('state');
            $table->string('block');
            $table->string('street');
            $table->string('exterior_number');
            $table->string('interior_number');
            $table->string('email');
            $table->string('phone_number');
            $table->string('salary');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('locality_id')->references('id')->on('localities')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
