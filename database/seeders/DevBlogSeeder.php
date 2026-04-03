<?php

namespace Database\Seeders;

use App\Modules\Websites\Models\Site;
use App\Modules\Websites\Models\SitePost;
use App\Modules\Websites\Models\SitePostCategory;
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
            $categories = collect();
            $categoryNames = ['Technology', 'Marketing', 'Business', 'Updates'];

            foreach ($categoryNames as $name) {
                $categories->push(SitePostCategory::withTrashed()->updateOrCreate(
                    ['site_id' => $site->id, 'slug' => Str::slug($name)],
                    [
                        'name' => $name,
                        'description' => "Test category for {$name}",
                        'deleted_at' => null,
                    ]
                ));
            }

            for ($j = 1; $j <= 5; $j++) {
                $title = "Test Post {$j} for Site {$site->id}";
                $slug = Str::slug($title);

                $post = SitePost::withTrashed()->updateOrCreate(
                    ['site_id' => $site->id, 'slug' => $slug],
                    [
                        'title' => $title,
                        'content' => "This is a test blog post content for {$title}.",
                        'published_at' => now()->subDays(rand(1, 30)),
                        'deleted_at' => null,
                    ]
                );

                // Sync categories
                $randomCategories = $categories->random(rand(1, 2));
                $post->categories()->sync($randomCategories->pluck('id'));
            }
        }
    }
}
