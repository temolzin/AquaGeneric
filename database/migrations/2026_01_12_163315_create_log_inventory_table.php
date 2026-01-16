<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogInventoryTable extends Migration
{
    public function up()
    {
        Schema::create('log_inventory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_id');
            $table->unsignedBigInteger('locality_id');
            $table->unsignedBigInteger('created_by');
            $table->integer('previous_amount');
            $table->integer('amount');
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->foreign('inventory_id')->references('id')->on('inventory')->onDelete('cascade');
            $table->foreign('locality_id')->references('id')->on('localities')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_inventory');
    }
}
