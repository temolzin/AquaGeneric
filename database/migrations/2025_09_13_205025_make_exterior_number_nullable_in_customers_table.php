<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MakeExteriorNumberNullableInCustomersTable extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE customers MODIFY exterior_number VARCHAR(255) NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE customers MODIFY exterior_number VARCHAR(255) NOT NULL');
    }
}