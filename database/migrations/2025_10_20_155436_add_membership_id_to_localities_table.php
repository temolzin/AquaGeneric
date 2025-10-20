<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMembershipIdToLocalitiesTable extends Migration
{
    public function up(): void
    {
        Schema::table('localities', function (Blueprint $table) {
            $table->unsignedBigInteger('membership_id')->nullable()->after('zip_code');
            $table->foreign('membership_id')->references('id')->on('memberships')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('localities', function (Blueprint $table) {
            $table->dropForeign(['membership_id']);
            $table->dropColumn('membership_id');
        });
    }

}
