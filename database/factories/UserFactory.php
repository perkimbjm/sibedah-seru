<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Role;
use App\Models\User;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'google_id' => $this->faker->word(),
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'avatar' => $this->faker->word(),
            'password' => $this->faker->password(),
            'role_id' => Role::factory(),
            'email_verified_at' => $this->faker->dateTime(),
            'phone' => $this->faker->phoneNumber(),
        ];
    }
}
