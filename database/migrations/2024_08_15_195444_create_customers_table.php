<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Costs;
use App\Models\Customer;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cost_id');
            $table->unsignedBigInteger('locality_id');
            $table->unsignedBigInteger('created_by');
            $table->string('name');
            $table->string('last_name');
            $table->string('block');
            $table->string('street');
            $table->string('interior_number');
            $table->boolean('marital_status');
            $table->string('partner_name')->nullable();
            $table->boolean('has_water_connection');
            $table->boolean('has_store');
            $table->boolean('has_all_payments');
            $table->boolean('has_water_day_night');
            $table->integer('occupants_number');
            $table->integer('water_days');
            $table->boolean('has_water_pressure');
            $table->boolean('has_cistern');
            $table->boolean('status');
            $table->string('responsible_name')->nullable();
            $table->timestamps();
            $table->softDeletes();
          
            $table->foreign('cost_id')->references('id')->on('costs')->onDelete('cascade');
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
        Schema::dropIfExists('customers');
    }
}
