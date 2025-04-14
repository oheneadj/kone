<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            //  $table->string('name')->unique();
            // $table->string('slug')->unique();
            // $table->foreignId('category_id')->constrained()->nullable();
            // $table->enum('status', ['draft', 'published', 'disabled'])->default('published');
            // $table->dateTime('published_at')->nullable();
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'slug' => $this->faker->slug,
            'user_id' => \App\Models\User::factory(),
            'category_id' => \App\Models\Category::factory(),
            'tag_id' => \App\Models\Tag::factory(),

        ];
    }
}
