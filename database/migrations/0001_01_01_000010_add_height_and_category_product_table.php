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
            $table->decimal('height_meters', 5, 2)->nullable()->after('era');
        });

        Schema::create('category_product', function (Blueprint $table) {
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->primary(['category_id', 'product_id']);
        });

        DB::table('products')
            ->orderBy('id')
            ->get(['id', 'category_id'])
            ->each(function ($product) {
                if (! $product->category_id) {
                    return;
                }

                DB::table('category_product')->insertOrIgnore([
                    'category_id' => $product->category_id,
                    'product_id' => $product->id,
                ]);
            });

        $demoHeights = [1.80, 6.50, 12.00];

        DB::table('products')
            ->orderBy('id')
            ->get(['id'])
            ->values()
            ->each(function ($product, $index) use ($demoHeights) {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update([
                        'height_meters' => $demoHeights[$index % count($demoHeights)],
                    ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_product');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('height_meters');
        });
    }
};