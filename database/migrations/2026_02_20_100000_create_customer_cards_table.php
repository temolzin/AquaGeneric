<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('alias', 30)->nullable();
            $table->string('openpay_card_id')->unique();
            $table->string('brand', 30);
            $table->string('last_four', 4);
            $table->string('holder_name');
            $table->string('expiration_month', 2);
            $table->string('expiration_year', 2);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->index('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_cards');
    }
}
