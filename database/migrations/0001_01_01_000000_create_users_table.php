<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Migración: create_users_table
 * --------------------------------
 * Crea las tres tablas de autenticación base de Laravel:
 *
 *   users                 → Usuarios registrados en la aplicación.
 *   password_reset_tokens → Tokens para el flujo de restablecimiento de contraseña.
 *   sessions              → Sesiones de usuario almacenadas en BD (driver 'database').
 *
 * Esta migración se ejecuta PRIMERO (prefijo 000000) porque todas las demás
 * tablas con user_id dependen de 'users'.
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();                      // El email es el identificador de login; debe ser único nullable() → permite usuarios que aún no han verificado su email
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');                             // Almacenado como hash bcrypt (nunca texto plano)
            $table->boolean('is_admin')->default(false);            // true → acceso al panel /admin
            $table->rememberToken();                                // rememberToken() → agrega la columna 'remember_token' VARCHAR(100) NULL usada cuando el usuario elige "Recordarme" al
                                                                    // iniciar sesión
            $table->timestamps();
            $table->softDeletes();                                  // softDeletes() → agrega deleted_at nullable. Los registros "eliminados" quedan en BD pero Laravel los excluye de todos
                                                                    // los queries automáticamente. Permite conservar el historial de órdenes.
            $table->string('photo')->nullable()->after('email');    // URL o path de la foto de perfil del usuario, opcional y nullable
            $table->date('birthdate')->nullable()->after('photo');  // Fecha de nacimiento del usuario, opcional y nullable
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();             // email como clave primaria → un solo token de reset por email a la vez
            $table->string('token');                        // Hash del token enviado por email (nunca el token en claro)
            $table->timestamp('created_at')->nullable();    // Para expirar tokens viejos
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();                       // ID de sesión alfanumérico generado por PHP
            $table->foreignId('user_id')->nullable()->index();     // nullable() → sesiones de visitantes anónimos no tienen user_id
            $table->string('ip_address', 45)->nullable();          // varchar(45) → soporta IPv4 (max 15 chars), IPv6 (max 39 chars) y mapeos IPv4-en-IPv6
            $table->text('user_agent')->nullable();                // Cadena del navegador/cliente
            $table->longText('payload');                           // Datos de sesión serializados (potencialmente grandes)
            $table->integer('last_activity')->index();             // integer en lugar de timestamp → Unix timestamp; indexado para limpiar sesiones expiradas
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
