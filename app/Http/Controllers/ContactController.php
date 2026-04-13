<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Mostrar formulario de contacto
     * GET /contact
     */
    public function show()
    {
        return view('contact');
    }

    /**
     * Procesar formulario de contacto
     * POST /contact
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

        // Aquí se podría guardar en BD o enviar email
        // Por ahora solo mostramos mensaje de éxito
        
        \Log::info('Contacto recibido', $validated);

        return redirect()->route('contact')
            ->with('success', '¡Gracias por tu mensaje! Nos pondremos en contacto pronto.');
    }
}
