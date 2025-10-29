<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ReplaceCategoryWithInventoryCategoryIdInInventory extends Migration
{
    public function up()
    {
        $categoryMapping = [
            'medidores' => 'Medidores de Agua',
            'tuberías' => 'Tuberías y Conexiones',
            'válvulas' => 'Válvulas y Reguladores'
        ];

        Schema::table('inventory', function (Blueprint $table) {
            $table->unsignedBigInteger('inventory_category_id')->nullable()->after('amount');
        });

        foreach ($categoryMapping as $oldCategory => $newCategoryName) {
            $categoryId = DB::table('inventory_categories')
                ->where('name', $newCategoryName)
                ->value('id');

            if ($categoryId) {
                DB::table('inventory')
                    ->where('category', $oldCategory)
                    ->update(['inventory_category_id' => $categoryId]);
            }
        }

        Schema::table('inventory', function (Blueprint $table) {
            $table->dropColumn('category');
            $table->foreign('inventory_category_id')
                ->references('id')
                ->on('inventory_categories')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropForeign(['inventory_category_id']);
            $table->string('category')->nullable();
        });

        $categoryMapping = [
            'Medidores de Agua' => 'medidores',
            'Tuberías y Conexiones' => 'tuberías', 
            'Válvulas y Reguladores' => 'válvulas'
        ];

        foreach ($categoryMapping as $newCategoryName => $oldCategory) {
            $categoryId = DB::table('inventory_categories')
                ->where('name', $newCategoryName)
                ->value('id');

            if ($categoryId) {
                DB::table('inventory')
                    ->where('inventory_category_id', $categoryId)
                    ->update(['category' => $oldCategory]);
            }
        }

        Schema::table('inventory', function (Blueprint $table) {
            $table->dropColumn('inventory_category_id');
        });
    }
}
