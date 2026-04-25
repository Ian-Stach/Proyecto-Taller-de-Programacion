<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('habitat')->nullable()->after('category_id')->index();
            $table->string('diet')->nullable()->after('habitat')->index();
            $table->string('era')->nullable()->after('diet')->index();
        });

        $habitats = ['terrestre', 'acuatico', 'volador'];
        $diets = ['carnivoro', 'herbivoro', 'omnivoro'];
        $eras = ['triasico', 'jurasico', 'cretacico'];

        DB::table('products')
            ->orderBy('id')
            ->get(['id'])
            ->values()
            ->each(function ($product, $index) use ($habitats, $diets, $eras) {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update([
                        'habitat' => $habitats[$index % count($habitats)],
                        'diet' => $diets[$index % count($diets)],
                        'era' => $eras[$index % count($eras)],
                    ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['habitat']);
            $table->dropIndex(['diet']);
            $table->dropIndex(['era']);
            $table->dropColumn(['habitat', 'diet', 'era']);
        });
    }
};