<?php

namespace Database\Seeders;

use App\Models\Site;
use App\Models\SitePost;
use App\Models\SitePostCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DevBlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sites = Site::all();

        foreach ($sites as $site) {
            // Create categories for this site
            $categories = collect();
            for ($i = 1; $i <= 3; $i++) {
                $name = fake()->unique()->words(2, true);
                $categories->push(SitePostCategory::create([
                    'site_id' => $site->id,
                    'name' => ucfirst($name),
                    'slug' => Str::slug($name),
                    'description' => fake()->sentence(),
                ]));
            }

            // Create posts for this site
            for ($j = 1; $j <= 10; $j++) {
                $title = fake()->sentence();
                $post = SitePost::create([
                    'site_id' => $site->id,
                    'title' => $title,
                    'slug' => Str::slug($title),
                    'content' => fake()->paragraphs(3, true),
                    'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
                ]);

                // Randomly associate posts with categories
                $randomCategories = $categories->random(rand(1, 2));
                $post->categories()->attach($randomCategories->pluck('id'));
            }
        }
    }
}
