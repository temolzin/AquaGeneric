<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MakeCustomerFieldsNullable extends Migration
{
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'name_tmp')) {
                $table->string('name_tmp')->nullable()->after('id');
            }
            if (!Schema::hasColumn('customers', 'last_name_tmp')) {
                $table->string('last_name_tmp')->nullable()->after('name_tmp');
            }
            if (!Schema::hasColumn('customers', 'email_tmp')) {
                $table->string('email_tmp')->nullable()->after('last_name_tmp');
            }
        });

        DB::table('customers')->update([
            'name_tmp' => DB::raw('name'),
            'last_name_tmp' => DB::raw('last_name'),
            'email_tmp' => DB::raw('email'),
        ]);

        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('customers', 'last_name')) {
                $table->dropColumn('last_name');
            }
            if (Schema::hasColumn('customers', 'email')) {
                $table->dropColumn('email');
            }
        });

        DB::statement('ALTER TABLE customers CHANGE name_tmp name VARCHAR(255)');
        DB::statement('ALTER TABLE customers CHANGE last_name_tmp last_name VARCHAR(255)');
        DB::statement('ALTER TABLE customers CHANGE email_tmp email VARCHAR(255)');
    }

    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'name_tmp')) {
                $table->string('name_tmp')->nullable(false)->after('id');
            }
            if (!Schema::hasColumn('customers', 'last_name_tmp')) {
                $table->string('last_name_tmp')->nullable(false)->after('name_tmp');
            }
            if (!Schema::hasColumn('customers', 'email_tmp')) {
                $table->string('email_tmp')->nullable(false)->after('last_name_tmp');
            }
        });

        DB::table('customers')->update([
            'name_tmp' => DB::raw('name'),
            'last_name_tmp' => DB::raw('last_name'),
            'email_tmp' => DB::raw('email'),
        ]);

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['name', 'last_name', 'email']);
        });

        DB::statement('ALTER TABLE customers CHANGE name_tmp name VARCHAR(255)');
        DB::statement('ALTER TABLE customers CHANGE last_name_tmp last_name VARCHAR(255)');
        DB::statement('ALTER TABLE customers CHANGE email_tmp email VARCHAR(255)');
    }
}
