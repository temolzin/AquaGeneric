<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaterConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('water_connections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('locality_id');
            $table->unsignedBigInteger('cost_id');
            $table->unsignedBigInteger('section_id');
            $table->unsignedBigInteger('created_by');
            $table->string('name');
            $table->integer('occupants_number');
            $table->json('water_days');
            $table->boolean('has_water_pressure');
            $table->boolean('has_cistern');
            $table->enum('type', ['residencial', 'commercial'])->default('residencial');
            $table->string('block');
            $table->string('street');
            $table->string('exterior_number');
            $table->string('interior_number');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('locality_id')->references('id')->on('localities')->onDelete('cascade');
            $table->foreign('cost_id')->references('id')->on('costs')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('water_connections');
    }
}
