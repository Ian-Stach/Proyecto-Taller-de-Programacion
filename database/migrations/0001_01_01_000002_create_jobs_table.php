<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Migración: create_jobs_table
 * --------------------------------
 * Crea las tablas para el sistema de colas (Queue) de Laravel con driver 'database'.
 *
 *   jobs         → Cola principal de trabajos pendientes de ejecución.
 *   job_batches  → Grupos de jobs (Bus::batch()) para procesamiento paralelo con seguimiento.
 *   failed_jobs  → Registro de jobs que fallaron tras agotar sus intentos máximos.
 *
 * Necesaria si QUEUE_CONNECTION=database en .env. Los workers consumen la tabla 'jobs'.
 * Los timestamps en jobs/job_batches son Unix timestamps (int), no columnas TIMESTAMP.
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index(); // Nombre de la cola (ej: 'default', 'emails'); indexado para el worker
            $table->longText('payload'); // Clase del job serializada (puede ser grande con adjuntos)
            $table->unsignedTinyInteger('attempts'); // Veces que se intentó ejecutar (0-255)
            // null → el job no está siendo procesado; se rellena cuando un worker lo toma
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at'); // Unix timestamp desde el que el job puede ejecutarse (delay)
            $table->unsignedInteger('created_at');   // Unix timestamp (no el helper timestamps())
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary(); // UUID del batch
            $table->string('name');          // Nombre descriptivo dado al batch
            $table->integer('total_jobs');   // Total de jobs en el batch al crearlo
            $table->integer('pending_jobs'); // Jobs aún pendientes (decrementa al completarse)
            $table->integer('failed_jobs');  // Jobs que fallaron
            $table->longText('failed_job_ids'); // JSON array con los IDs de jobs fallidos
            $table->mediumText('options')->nullable(); // Opciones serializadas (callbacks then/catch/finally)
            $table->integer('cancelled_at')->nullable(); // Unix timestamp de cancelación (null si no cancelado)
            $table->integer('created_at');
            $table->integer('finished_at')->nullable(); // null hasta que todos los jobs terminan
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique(); // UUID único; usado por Bus::chain() para deduplicar
            $table->text('connection');       // Driver de cola usado (ej: 'database')
            $table->text('queue');            // Nombre de la cola en la que falló
            $table->longText('payload');      // Payload del job serializado (para reintentar manualmente)
            $table->longText('exception');    // Stack trace completo de la excepción
            // useCurrent() → equivale a DEFAULT CURRENT_TIMESTAMP; se rellena automáticamente al insertar
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
