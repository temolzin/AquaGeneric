<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpenpayTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('openpay_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_id');

            $table->unsignedBigInteger('locality_id');

            $table->string('openpay_transaction_id', 255);
            $table->string('openpay_customer_id', 255)->nullable()->comment('ID del cliente en OpenPay');
            $table->string('openpay_order_id', 255)->nullable()->comment('ID del pedido en OpenPay');

            $table->decimal('amount', 10, 2);
            $table->decimal('amount_refunded', 10, 2)->default(0);
            $table->string('currency', 3)->default('MXN');
            $table->string('status', 50);
            $table->text('description')->nullable();

            $table->json('payment_method')->nullable()->comment('Método de pago completo (tarjeta, banco, tienda)');

            $table->string('card_brand', 50)->nullable();
            $table->string('card_last4', 4)->nullable();
            $table->string('card_holder_name', 150)->nullable();
            $table->string('expiration_month', 2)->nullable();
            $table->string('expiration_year', 4)->nullable();

            $table->string('store_reference', 100)->nullable();
            $table->string('store_barcode_url', 500)->nullable();
            $table->string('store_name', 100)->nullable();
            $table->dateTime('store_expiration_date')->nullable();

            $table->json('metadata')->nullable()->comment('Metadatos adicionales');

            $table->dateTime('openpay_created_at')->nullable();
            $table->dateTime('openpay_updated_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('openpay_transaction_id', 'idx_openpay_transaction_id');
            $table->index('openpay_customer_id', 'idx_openpay_customer_id');
            $table->index('payment_id', 'idx_payment_id');
            $table->index('locality_id', 'idx_locality_id');
            $table->index('status', 'idx_status');

            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
            $table->foreign('locality_id')->references('id')->on('localities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('openpay_transactions');
    }
}
