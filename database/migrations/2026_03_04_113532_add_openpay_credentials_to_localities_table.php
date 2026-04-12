<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOpenpayCredentialsToLocalitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('localities', function (Blueprint $table) {
            $table->string('openpay_merchant_id')->nullable()->after('membership_assigned_at');
            $table->text('openpay_private_key')->nullable()->after('openpay_merchant_id');
            $table->string('openpay_public_key')->nullable()->after('openpay_private_key');
            $table->string('openpay_webhook_user')->nullable()->after('openpay_public_key');
            $table->string('openpay_webhook_password')->nullable()->after('openpay_webhook_user');
            $table->boolean('openpay_sandbox')->default(true)->after('openpay_webhook_password');
            $table->boolean('openpay_enabled')->default(false)->after('openpay_sandbox');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('localities', function (Blueprint $table) {
            $table->dropColumn([
                'openpay_merchant_id',
                'openpay_private_key',
                'openpay_public_key',
                'openpay_webhook_user',
                'openpay_webhook_password',
                'openpay_sandbox',
                'openpay_enabled',
            ]);
        });
    }
}
