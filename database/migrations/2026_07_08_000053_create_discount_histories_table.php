<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('discount_histories', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('locality_id');
            $table->unsignedBigInteger('discount_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            $table->string('module');
            $table->unsignedBigInteger('record_id');

            $table->decimal('original_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2);
            $table->decimal('discount_percentage', 5, 2);
            $table->decimal('final_amount', 10, 2);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('locality_id')->references('id')->on('localities')->onDelete('cascade');
            $table->foreign('discount_id')->references('id')->on('discounts')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('discount_histories');
    }
}
