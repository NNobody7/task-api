<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Note>
 */
class NoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $bContent = $this->faker->boolean(75);
        return [
            'title' => $this->faker->sentence(5),
            'content' => $bContent ? $this->faker->paragraph() : null
        ];
    }
}
