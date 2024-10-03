<?php

namespace Database\Factories;
use App\Models\Activity;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Activity::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'performed_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'points' => 20,
        ];
    }
}
