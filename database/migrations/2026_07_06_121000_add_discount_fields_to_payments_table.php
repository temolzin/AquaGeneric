<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'discount_id')) {
                $table->unsignedBigInteger('discount_id')->nullable()->after('has_discount');
            }
            if (!Schema::hasColumn('payments', 'discount_percentage')) {
                $table->decimal('discount_percentage', 8, 2)->nullable()->after('discount_id');
            }
            if (!Schema::hasColumn('payments', 'discount_amount')) {
                $table->decimal('discount_amount', 12, 2)->nullable()->after('discount_percentage');
            }
            if (!Schema::hasColumn('payments', 'final_amount')) {
                $table->decimal('final_amount', 12, 2)->nullable()->after('discount_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'final_amount')) {
                $table->dropColumn('final_amount');
            }
            if (Schema::hasColumn('payments', 'discount_amount')) {
                $table->dropColumn('discount_amount');
            }
            if (Schema::hasColumn('payments', 'discount_percentage')) {
                $table->dropColumn('discount_percentage');
            }
            if (Schema::hasColumn('payments', 'discount_id')) {
                $table->dropColumn('discount_id');
            }
        });
    }
};
