<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Migración: create_contacts_table
 * ----------------------------------------
 * Crea la tabla para almacenar los mensajes del formulario de contacto.
 *
 * Diseño intencional:
 *   - No tiene user_id: permite envíos anónimos sin requerir login.
 *   - No tiene la columna 'agree': el campo de aceptación de términos es solo
 *     de validación (FormRequest) y no se persiste en BD.
 *   - Los campos de texto (name, email, phone) tienen límite explícito de
 *     caracteres para evitar datos excesivamente largos.
 *   - El campo subject usa ENUM con los valores permitidos (lista cerrada),
 *     reforzando la validación a nivel de BD además del FormRequest.
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);    // VARCHAR(100): nombre del remitente
            $table->string('email', 100);   // VARCHAR(100): email de contacto (no FK a users)
            $table->string('phone', 20)->nullable(); // Teléfono opcional; 20 chars soporta formatos internacionales
            // ENUM → el motor de BD rechaza cualquier valor fuera de esta lista
            // Debe mantenerse sincronizado con la validación 'in:...' del FormRequest
            $table->enum('subject', ['consulta_general', 'soporte_producto', 'pedido', 'sugerencia', 'reclamo', 'otro']);
            $table->text('message'); // Cuerpo del mensaje; sin límite de longitud
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
