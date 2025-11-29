<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomeTypesTable extends Migration
{
    public function up()
    {
        Schema::create('income_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color')->default('#6c757d');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('locality_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('locality_id')->references('id')->on('localities')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('income_types');
    }
}
