<?php

namespace Database\Factories;

use App\Models\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderStatusFactory extends Factory
{
    protected $model = OrderStatus::class;

    public function definition(): array
    {
        $name = $this->faker->randomElement([
            'Pendiente',
            'Procesando',
            'Enviado',
            'Entregado',
            'Cancelado',
            'Reembolsado',
            'Fallido',
            'En Revisión',
            'Confirmado',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'color' => $this->faker->hexColor(),
            'is_active' => $this->faker->boolean(80),
            'is_default' => false,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
            'is_active' => true,
        ]);
    }
}
