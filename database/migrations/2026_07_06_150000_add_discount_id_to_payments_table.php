<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddDiscountIdToPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('payments', 'discount_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->unsignedBigInteger('discount_id')->nullable();
                $table->foreign('discount_id')->references('id')->on('discounts')->onDelete('set null');
            });
        }

        DB::statement('ALTER TABLE discounts MODIFY locality_id BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'discount_id')) {
                $table->dropForeign(['discount_id']);
                $table->dropColumn('discount_id');
            }
        });

        DB::statement('ALTER TABLE discounts MODIFY locality_id BIGINT UNSIGNED NOT NULL');
    }
}
