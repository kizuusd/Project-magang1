<?php

namespace Modules\Keuangan\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Keuangan\Models\Category;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Category>
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->unique()->randomElement([
                'Gaji', 'Freelance', 'Investasi', 'Bonus', 'Dividen',
                'Makanan', 'Transportasi', 'Hiburan', 'Belanja',
                'Tagihan', 'Kesehatan', 'Pendidikan', 'Sewa',
            ]),
            'type' => fake()->randomElement(['income', 'expense']),
        ];
    }

    /**
     * Indicate that the category is an income type.
     */
    public function income(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'income',
        ]);
    }

    /**
     * Indicate that the category is an expense type.
     */
    public function expense(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'expense',
        ]);
    }
}
