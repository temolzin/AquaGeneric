<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralEarningsTable extends Migration
{
    public function up()
    {
        Schema::create('general_earnings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('locality_id');
            $table->unsignedBigInteger('created_by');
            $table->string('concept');
            $table->text('description');
            $table->decimal('amount', 10, 2);

            $table->unsignedBigInteger('earning_type_id')->nullable();

            $table->date('earning_date');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('locality_id')->references('id')->on('localities')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('earning_type_id')->references('id')->on('earning_types')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('general_earnings');
    }
}
