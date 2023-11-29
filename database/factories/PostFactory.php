<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence;
        $slug = Str::slug($title);

        return [
            'post_category_id' => rand(1, 4),
            'slug' => $slug,
            'title_hy' => $title,
            'title_en' => $title,
            'text_hy' => fake()->text(),
            'text_en' => fake()->text(),
            'description_hy' => fake()->text(),
            'description_en' => fake()->text(),
            'image' => 'images/posts/article-img-1.png',
        ];
    }
}
