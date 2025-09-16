<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeExteriorNumberNullableInCustomersTable extends Migration
{
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('exterior_number')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('exterior_number')->nullable(false)->change();
        });
    }
}
