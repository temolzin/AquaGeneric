<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovementsHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movements_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alter_by');
            $table->timestamps();
            $table->string('module');
            $table->string('action');
            $table->json('record_id');
            $table->json('before_data');
            $table->json('current_data')->nullable();

            $table->foreign('alter_by')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movements_history');
    }
}
