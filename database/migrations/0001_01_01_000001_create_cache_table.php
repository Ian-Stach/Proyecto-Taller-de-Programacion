<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Migración: create_cache_table
 * --------------------------------
 * Crea las tablas para el driver de caché 'database' de Laravel.
 *
 *   cache       → Almacena los valores cacheados.
 *   cache_locks → Bloqueos atómicos para el driver de caché (Cache::lock()).
 *
 * Necesaria si CACHE_STORE=database está configurado en .env.
 * Las claves de caché usan el prefijo definido en config/cache.php.
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary(); // La clave de caché es el PK (no hay id autoincremental)
            $table->mediumText('value'); // mediumText soporta hasta ~16MB; suficiente para datos grandes
            // bigInteger como Unix timestamp → permite comparar expiración con time() eficientemente
            $table->bigInteger('expiration')->index();
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary(); // Nombre del lock (ej: 'checkout-user-5')
            $table->string('owner'); // Identifica qué proceso/request posee el lock
            $table->bigInteger('expiration')->index(); // Unix timestamp de expiración del lock
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
