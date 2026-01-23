<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpenpayWebhooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('openpay_webhooks', function (Blueprint $table) {
            $table->id();

            $table->string('event_id', 255);
            $table->string('event_type', 100);
            $table->string('transaction_id', 255)->nullable();
            $table->string('customer_id', 255)->nullable();

            $table->string('status', 50);
            $table->json('payload');

            $table->boolean('processed')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->text('processing_error')->nullable();

            $table->integer('processing_attempts')->default(0);
            $table->timestamp('last_attempt_at')->nullable();

            $table->timestamp('openpay_created_at')->nullable();
            $table->timestamp('openpay_updated_at')->nullable();

            $table->timestamps();

            $table->index('event_id', 'idx_event_id');
            $table->index('transaction_id', 'idx_transaction_id');
            $table->index('customer_id', 'idx_customer_id');
            $table->index('event_type', 'idx_event_type');
            $table->index('processed', 'idx_processed');
            $table->index('created_at', 'idx_created_at');

            $table->index(['processed', 'created_at'], 'idx_processed_created');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('openpay_webhooks');
    }
}
