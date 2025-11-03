<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMembershipIdToLocalitiesTable extends Migration
{
    public function up()
    {
        Schema::table('localities', function (Blueprint $table) {
            if (!Schema::hasColumn('localities', 'membership_id')) {
                $table->foreignId('membership_id')
                    ->nullable()
                    ->constrained('memberships')
                    ->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('localities', function (Blueprint $table) {
            if (Schema::hasColumn('localities', 'membership_id')) {
                $table->dropForeign(['membership_id']);
                $table->dropColumn('membership_id');
            }
        });
    }
}
