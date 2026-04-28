<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Migración: update_category_name_uniqueness_for_tree_paths
 * -----------------------------------------------------------
 * Reemplaza la restricción de unicidad global en 'name' por una unicidad
 * compuesta en (parent_id, name).
 *
 * ANTES (migración 000003): UNIQUE(name)
 *   → Imposible tener dos categorías llamadas 'Grande' aunque sean hijas de
 *     padres distintos.
 *
 * DESPUÉS (esta migración): UNIQUE(parent_id, name)
 *   → Solo se prohíbe el mismo nombre dentro del mismo nivel y mismo padre.
 *   → 'Grande' bajo 'Carnívoros' y 'Grande' bajo 'Herbívoros' son válidas.
 *
 * NOTA sobre NULLs en la restricción compuesta:
 *   En SQLite y MySQL, NULL != NULL en restricciones UNIQUE, por lo que
 *   varias categorías raíz con el mismo nombre podrían ser aceptadas por
 *   el motor. El FormRequest debe validar este caso si se quiere más control.
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Elimina la restricción global por nombre (generada automáticamente por Laravel)
            $table->dropUnique('categories_name_unique');
            // Crea restricción compuesta: mismo nombre sólo bloqueado dentro del mismo padre
            $table->unique(['parent_id', 'name'], 'categories_parent_id_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique('categories_parent_id_name_unique');
            // Restaura la restricción global original
            $table->unique('name');
        });
    }
};