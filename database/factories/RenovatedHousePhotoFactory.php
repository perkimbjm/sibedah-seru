<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\House;
use App\Models\HousePhoto;

class HousePhotoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = HousePhoto::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'renovated_house_id' => House::factory(),
            'photo_url' => $this->faker->word(),
            'description' => $this->faker->text(),
            'progres' => $this->faker->randomFloat(0, 0, 9999999999.),
            'is_primary' => $this->faker->boolean(),
            'is_best' => $this->faker->boolean(),
        ];
    }
}
