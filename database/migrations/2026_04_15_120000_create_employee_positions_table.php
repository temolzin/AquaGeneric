<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeePositionsTable extends Migration
{
    public function up()
    {
        Schema::create('employee_positions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color')->default('#6c757d');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('locality_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('locality_id')->references('id')->on('localities')->onDelete('set null');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('position_id')->nullable()->after('locality_id');
            $table->foreign('position_id')->references('id')->on('employee_positions')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['position_id']);
            $table->dropColumn('position_id');
        });

        Schema::dropIfExists('employee_positions');
    }
}
