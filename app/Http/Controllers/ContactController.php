<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

/*
 * ContactController
 * ------------------
 * Maneja el formulario de contacto del sitio.
 * Rutas (ver routes/web.php, con throttle:20,1 en el POST):
 *   GET  /contact → show()  → muestra el formulario
 *   POST /contact → store() → valida, persiste y redirige
 *
 * El throttle:20,1 en la ruta POST limita a 20 envíos por minuto por IP,
 * protegiendo contra spam sin necesitar CAPTCHA.
 */
class ContactController extends Controller
{
    /**
     * Mostrar formulario de contacto
     * GET /contact
     *
     * Solo renderiza la vista. No necesita pasar datos porque el formulario
     * usa old() para repoblar los campos tras una validación fallida.
     */
    public function show()
    {
        return view('contact');
    }

    /**
     * Procesar formulario de contacto
     * POST /contact
     *
     * Validaciones clave:
     *   name    → max:100 para evitar desbordamientos en la columna VARCHAR de BD.
     *   email   → regla 'email' valida formato RFC; max:100 igual que name.
     *   phone   → nullable: el teléfono es opcional en el formulario.
     *   subject → 'in:...' lista blanca de categorías válidas; rechaza cualquier
     *             valor arbitrario que el usuario pueda enviar directamente.
     *   message → min:10 para filtrar mensajes vacíos o sin contenido real.
     *   agree   → 'accepted' valida que el checkbox de términos esté marcado (valor '1' o 'true').
     *
     * Contact::create($validated) usa el array ya validado y permite que Eloquent
     * lo pase por $fillable del modelo, evitando mass assignment de campos no deseados.
     *
     * \Log::info() registra el contacto en storage/logs/laravel.log para
     * auditoría básica sin depender de un sistema de notificaciones.
     *
     * Redirige con flash 'success' a la misma ruta de contacto para que el usuario
     * vea la confirmación y el formulario limpio.
     */
    public function store(Request $request)
    {
        // Validar entrada
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|in:consulta_general,soporte_producto,pedido,sugerencia,reclamo,otro',
            'message' => 'required|string|min:10|max:1000',
            'agree' => 'accepted',
        ]);

        // Guardar en BD
        Contact::create($validated);
        \Log::info('Contacto recibido', $validated);

        return redirect()->route('contact')
            ->with('success', '¡Gracias por tu mensaje! Nos pondremos en contacto pronto.');
    }
}
