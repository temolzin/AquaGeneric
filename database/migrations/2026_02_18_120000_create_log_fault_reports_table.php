<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogFaultReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_fault_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fault_report_id');
            $table->unsignedBigInteger('locality_id');
            $table->unsignedBigInteger('created_by');
            $table->enum('status', ['Pendiente', 'En revisión', 'Completado'])->default('Pendiente');
            $table->text('comentario')->nullable();
            $table->timestamps();

            $table->foreign('fault_report_id')->references('id')->on('fault_report')->onDelete('cascade');
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
        Schema::dropIfExists('log_fault_reports');
    }
}
