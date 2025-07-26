<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LogIncident extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_incidents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('locality_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('employee_id');
            $table->string('status');
            $table->text('description')->nullable();        
            $table->timestamps();

            $table->foreign('locality_id')->references('id')->on('localities')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_incidents');
    }
}
