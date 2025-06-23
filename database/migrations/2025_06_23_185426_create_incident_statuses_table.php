<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidentStatusesTable extends Migration
{
    public function up()
    {
        Schema::create('incident_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('locality_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('locality_id')->references('id')->on('localities')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('incident_statuses');
    }
}
