<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Download;

class DownloadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Download::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'year' => $this->faker->randomNumber(),
            'description' => $this->faker->text(),
            'slug' => $this->faker->slug(),
            'file_url' => $this->faker->text(),
        ];
    }
}
