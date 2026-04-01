<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDebtCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('debt_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color')->nullable();
            $table->unsignedBigInteger('locality_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('locality_id')->references('id')->on('localities')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        $exists = DB::table('debt_categories')
            ->where('name', 'Servicio de Agua')
            ->whereNull('locality_id')
            ->exists();

        if (! $exists) {
            DB::table('debt_categories')->insert([
                'name' => 'Servicio de Agua',
                'description' => 'Categoría global para Servicio de Agua',
                'color' => 'bg-primary',
                'locality_id' => null,
                'created_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        Schema::dropIfExists('debt_categories');
    }
}
