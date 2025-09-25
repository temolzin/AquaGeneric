<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('locality_id');
            $table->unsignedBigInteger('created_by');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->integer('amount')->unsigned()->default(0);
            $table->string('category', 50)->default('general');
            $table->string('material', 50)->nullable();
            $table->string('dimensions', 50)->nullable();
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
        Schema::dropIfExists('inventory');
    }
}
