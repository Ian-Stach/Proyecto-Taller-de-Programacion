<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// MustVerifyEmail está comentado intencionalmente: si se implementara, Laravel exigiría
// que el usuario verifique su email antes de acceder a rutas protegidas. En este
// proyecto la verificación no está activa como requisito de acceso.
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/*
 * User
 * -----
 * Modelo de usuario autenticable. Extiende Authenticatable de Laravel, que a su vez
 * implementa las interfaces de autenticación (Illuminate\Contracts\Auth\Authenticatable)
 * y provee la lógica de sesión, remember_token, etc.
 *
 * Tabla: users
 * Columnas relevantes:
 *   name              → nombre del usuario
 *   email             → email único, usado como credencial de login
 *   email_verified_at → null si no ha verificado; si MustVerifyEmail estuviera activo,
 *                       Laravel bloquearía el acceso hasta que no sea null.
 *   password          → hash bcrypt (cast 'hashed' lo hashea automáticamente al asignar)
 *   remember_token    → token de "recordarme" generado al hacer login con esa opción
 *
 * Atributos PHP 8 en vez de propiedades:
 *   #[Fillable([...])] equivale a protected $fillable = [...].
 *   #[Hidden([...])]   equivale a protected $hidden = [...].
 *   Son la forma moderna (Laravel 11+) de declarar estos comportamientos.
 *   'password' y 'remember_token' ocultos para que nunca se serialicen en JSON
 *   (por ejemplo, si se devuelve el usuario en una respuesta API).
 *
 * Traits:
 *   HasFactory   → permite usar UserFactory en seeders y tests.
 *   Notifiable   → habilita el sistema de notificaciones de Laravel (email, DB, etc.).
 *
 * Relaciones:
 *   orders()    → HasMany(Order)    — historial de órdenes del usuario
 *   favorites() → HasMany(Favorite) — productos marcados como favoritos
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // 'datetime' devuelve email_verified_at como instancia de Carbon (no string)
            'email_verified_at' => 'datetime',
            // 'hashed' hashea automáticamente el password con bcrypt al asignarlo:
            // $user->password = 'texto_plano' → se guarda el hash sin llamar a Hash::make()
            'password' => 'hashed',
        ];
    }

    // Relación: Un usuario tiene muchas órdenes
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Relación: Un usuario tiene muchos favoritos
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
}
