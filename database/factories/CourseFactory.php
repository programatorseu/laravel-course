<?php

namespace Database\Factories;

use App\Models\Type;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'type_id' => Type::factory(),
            'title' => $this->faker->sentence,
            'date'=> $this->faker->date(),
            'body' => $this->faker->sentence,
            'url' => $this->faker->slug,
        ];
    }
}
