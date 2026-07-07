<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('locality_id');
            $table->unsignedBigInteger('created_by');
            $table->string('name');
            $table->decimal('percentage',5,2);
            $table->string('color')->default('bg-secondary');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('locality_id') ->references('id') ->on('localities') ->onDelete('cascade');
            $table->foreign('created_by') ->references('id') ->on('users') ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discounts');
    }
}
