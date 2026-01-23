<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    private const PAYMENTS_METHODS = ['cash', 'card', 'transfer','openpay_card','openpay_bank','openpay_store'];
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

            $table->string('openpay_id', 255)->nullable()->after('method');
            $table->string('openpay_status', 50)->nullable()->after('openpay_id');
            $table->string('authorization', 100)->nullable()->after('openpay_status');
            $table->string('error_code', 50)->nullable()->after('authorization');
            $table->text('error_message')->nullable()->after('error_code');
            $table->string('card_brand', 50)->nullable()->after('error_message');
            $table->string('card_last4', 4)->nullable()->after('card_brand');
            $table->boolean('is_future_payment')->default(false)->after('card_last4');

            $table->timestamps();
            $table->softDeletes();
       
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('debt_id')->references('id')->on('debts')->onDelete('cascade');
            $table->foreign('locality_id')->references('id')->on('localities')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            $table->index('openpay_id', 'idx_openpay_id');
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
