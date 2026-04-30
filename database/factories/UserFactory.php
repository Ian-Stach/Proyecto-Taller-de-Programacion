<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/*
 * UserFactory
 * ------------
 * Genera usuarios de prueba para seeders y tests.
 *
 * Optimización de password con static::$password:
 *   Hash::make() es una operación costosa (bcrypt con múltiples rondas).
 *   El patrón ??= (null-coalescing assignment) hashea la contraseña UNA sola vez
 *   y la reutiliza en todas las instancias del factory durante la misma ejecución.
 *   Todos los usuarios de prueba tienen la contraseña 'password' (texto plano).
 *
 * Estado adicional:
 *   unverified() → método de estado que sobrescribe email_verified_at a null,
 *   simulando un usuario que no ha verificado su email.
 *   Se encadena al factory: User::factory()->unverified()->create()
 */

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /*
     * Caché estático del hash de 'password' para reutilizarlo entre instancias.
     * ?string = null hasta la primera llamada; luego persiste el hash generado.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            // safeEmail() genera emails seguros para pruebas (dominio example.com, etc.)
            // unique() garantiza que no se repitan en la misma ejecución del factory
            'email' => fake()->unique()->safeEmail(),
            // Los usuarios generados por el factory están verificados por defecto
            'email_verified_at' => now(),
            // ??= hashea 'password' solo la primera vez; reutiliza el hash las siguientes
            'password' => static::$password ??= Hash::make('password'),
            // remember_token simula que el usuario eligió "Recordarme" al iniciar sesión
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * state() devuelve una nueva instancia del factory con los atributos sobrescritos.
     * Al encadenar ->unverified(), email_verified_at queda en null, simulando
     * un usuario que nunca completó el flujo de verificación de email.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
