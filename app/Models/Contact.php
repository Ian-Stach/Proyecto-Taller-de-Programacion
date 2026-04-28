<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/*
 * Contact
 * --------
 * Registra los mensajes enviados desde el formulario de contacto del sitio.
 * No tiene relaciones con otros modelos: cada contacto es un registro independiente.
 *
 * Tabla: contacts
 * Columnas relevantes:
 *   name    → nombre del remitente (max 100)
 *   email   → email del remitente (max 100)
 *   phone   → teléfono opcional (nullable)
 *   subject → categoría del mensaje; validada con whitelist en ContactController
 *             (consulta_general | soporte_producto | pedido | sugerencia | reclamo | otro)
 *   message → cuerpo del mensaje (min 10, max 1000 caracteres)
 *
 * NOTA: 'agree' (checkbox de términos) no se persiste; es solo un campo de validación
 * en el request. El $fillable no lo incluye a propósito.
 *
 * No hay softDeletes ni estados: los contactos se guardan y se consultan manualmente
 * desde la BD o desde el log (ver ContactController@store que también llama a \Log::info).
 */
class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
    ];
}
