<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('openpay_webhook_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('verification_code', 50);
            $table->string('openpay_event_id', 100)->nullable();
            $table->timestamp('event_date')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->boolean('copied')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('openpay_webhook_verifications');
    }
};
