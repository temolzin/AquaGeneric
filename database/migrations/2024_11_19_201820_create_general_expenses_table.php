<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('locality_id');
            $table->unsignedBigInteger('created_by');
            $table->string('concept');
            $table->text('description');
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['mainteinence', 'services', 'supplies', 'taxes', 'staff']);
            $table->date('expense_date');
            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('general_expenses');
    }
}
