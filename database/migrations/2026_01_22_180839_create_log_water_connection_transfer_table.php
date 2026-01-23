<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_water_connection_transfer', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('water_connection_id');
            $table->unsignedBigInteger('old_customer_id');
            $table->unsignedBigInteger('new_customer_id');
            $table->string('reason', 50)->default('death');
            $table->date('effective_date')->default(DB::raw('CURRENT_DATE'));
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('water_connection_id') ->references('id')->on('water_connections');
            $table->foreign('old_customer_id') ->references('id')->on('customers');
            $table->foreign('new_customer_id') ->references('id')->on('customers');
            $table->foreign('created_by') ->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_water_connection_transfer');
    }
};
