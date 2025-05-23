<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    private const PAYMENTS_METHODS = ['cash', 'card', 'transfer'];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('debt_id');
            $table->unsignedBigInteger('locality_id');
            $table->unsignedBigInteger('created_by');
            $table->decimal('amount', 10, 2);
            $table->enum('method', self::PAYMENTS_METHODS);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
       
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('debt_id')->references('id')->on('debts')->onDelete('cascade');
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
        Schema::dropIfExists('payments');
    }
}
