<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Cost;
use App\Models\Locality;

class MakeLocalityIdNullableInCostsAndRemoveLocalityZero extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE costs MODIFY COLUMN locality_id BIGINT UNSIGNED NULL');

        Cost::where('locality_id', 0)->update(['locality_id' => null]);

        Locality::where('id', 0)->delete();
    }

   public function down()
    {
        $locality = Locality::first() ?? throw new \RuntimeException('No hay localidades disponibles para revertir la migración.');
        
        Cost::whereNull('locality_id')->update(['locality_id' => $locality->id]);

        DB::statement('ALTER TABLE costs MODIFY COLUMN locality_id BIGINT UNSIGNED NOT NULL');
    }
}
