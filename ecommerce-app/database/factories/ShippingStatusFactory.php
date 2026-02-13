<?php

namespace Database\Factories;

use App\Models\ShippingStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ShippingStatusFactory extends Factory
{
    protected $model = ShippingStatus::class;

    public function definition(): array
    {
        $name = $this->faker->randomElement([
            'Pendiente de Envío',
            'Preparando',
            'Enviado',
            'En Tránsito',
            'Entregado',
            'Devuelto',
            'Cancelado',
            'En Aduana',
            'Listo para Recoger',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'color' => $this->faker->hexColor(),
            'is_active' => $this->faker->boolean(80),
            'is_default' => false,
            'sort_order' => $this->faker->numberBetween(1, 100),
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
