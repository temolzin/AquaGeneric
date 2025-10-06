<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashClosuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_closures', function (Blueprint $table) {
            $table->id();
            $table->decimal('initial_amount', 12, 2)->default(0);
            $table->decimal('final_amount', 12, 2)->default(0);
            $table->dateTime('opened_at');
            $table->dateTime('closed_at')->nullable();
            $table->decimal('total_sales', 12, 2)->default(0);
            $table->decimal('total_expenses', 12, 2)->default(0);
            $table->timestamps();

            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('locality_id')->constrained('localities')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_closures');
    }
}
