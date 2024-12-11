<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\House;
use App\Models\Review;

class ReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Review::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'renovated_house_id' => House::factory(),
            'name' => $this->faker->name(),
            'comment' => $this->faker->text(),
            'rating' => $this->faker->randomNumber(),
        ];
    }
}
