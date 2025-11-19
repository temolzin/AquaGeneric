<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->unsignedBigInteger('status_id')->nullable()->after('category_id');
        });

        DB::table('incidents')->get()->each(function ($incident) {
            $statusId = DB::table('incident_statuses')->where('status', $incident->status)->value('id');

            if (!$statusId && $incident->status) {
                $statusId = DB::table('incident_statuses')->insertGetId([
                    'status' => $incident->status,
                    'description' => null,
                    'created_by' => $incident->created_by,   
                    'locality_id' => $incident->locality_id, 
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if ($statusId) {
                DB::table('incidents')
                    ->where('id', $incident->id)
                    ->update(['status_id' => $statusId]);
            }
        });

        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->foreign('status_id')->references('id')->on('incident_statuses')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->string('status')->nullable();
            $table->dropForeign(['status_id']);
        });

        DB::table('incidents')->get()->each(function ($incident) {
            $statusName = DB::table('incident_statuses')
                ->where('id', $incident->status_id)
                ->value('status');

            if ($statusName) {
                DB::table('incidents')
                    ->where('id', $incident->id)
                    ->update(['status' => $statusName]);
            }
        });

        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn('status_id');
        });
    }
};
