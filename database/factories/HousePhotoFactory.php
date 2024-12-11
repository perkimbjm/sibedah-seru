<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\HousePhoto;
use App\Models\Rtlh;

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
            'house_id' => Rtlh::factory(),
            'photo_url' => $this->faker->word(),
            'description' => $this->faker->text(),
            'rtlh_id' => Rtlh::factory(),
        ];
    }
}
