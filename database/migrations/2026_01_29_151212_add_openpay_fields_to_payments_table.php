<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('openpay_transaction_id')->nullable()->after('method');
            $table->string('openpay_order_id')->nullable()->after('openpay_transaction_id');
            $table->string('openpay_authorization')->nullable()->after('openpay_order_id');
            $table->enum('openpay_status', ['in_progress', 'completed', 'failed', 'cancelled', 'refunded'])
                ->nullable()
                ->after('openpay_authorization');
            $table->text('openpay_error_message')->nullable()->after('openpay_status');
            $table->json('openpay_card_data')->nullable()->after('openpay_error_message');
            $table->timestamp('openpay_processed_at')->nullable()->after('openpay_card_data');
        });

        DB::statement("ALTER TABLE payments MODIFY method ENUM('cash', 'card', 'transfer', 'openpay') NOT NULL DEFAULT 'cash'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE payments MODIFY method ENUM('cash', 'card', 'transfer') NOT NULL DEFAULT 'cash'");

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'openpay_transaction_id',
                'openpay_order_id',
                'openpay_authorization',
                'openpay_status',
                'openpay_error_message',
                'openpay_card_data',
                'openpay_processed_at'
            ]);
        });
    }
};
