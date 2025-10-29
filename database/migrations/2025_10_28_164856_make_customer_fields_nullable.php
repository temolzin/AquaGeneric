<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MakeCustomerFieldsNullable extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE customers MODIFY name VARCHAR(255) NULL');
        DB::statement('ALTER TABLE customers MODIFY last_name VARCHAR(255) NULL');
        DB::statement('ALTER TABLE customers MODIFY email VARCHAR(255) NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE customers MODIFY name VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE customers MODIFY last_name VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE customers MODIFY email VARCHAR(255) NOT NULL');
    }
}
