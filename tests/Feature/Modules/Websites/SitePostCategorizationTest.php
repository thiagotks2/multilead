<?php

namespace Tests\Feature\Modules\Websites;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitePostCategorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Disable activity logger during tests to prevent errors from undefined tap() method on LogOptions in existing models
        \Spatie\Activitylog\Facades\Activity::disableLogging();
    }

    public function test_it_can_attach_multiple_categories_to_a_post(): void
    {
        $company = \App\Modules\Identity\Models\Company::factory()->create();
        $site = \App\Modules\Websites\Models\Site::create([
            'company_id' => $company->id,
            'name' => 'Test Site',
            'status' => 'development',
        ]);

        $post = \App\Modules\Websites\Models\SitePost::create([
            'site_id' => $site->id,
            'title' => 'Test Post',
            'slug' => 'test-post',
            'content' => 'Content here',
        ]);

        $category1 = \App\Modules\Websites\Models\SitePostCategory::create([
            'site_id' => $site->id,
            'name' => 'Category 1',
            'slug' => 'cat-1',
        ]);

        $category2 = \App\Modules\Websites\Models\SitePostCategory::create([
            'site_id' => $site->id,
            'name' => 'Category 2',
            'slug' => 'cat-2',
        ]);

        // Attach categories
        $post->categories()->attach([$category1->id, $category2->id]);

        $this->assertCount(2, $post->categories);
        $this->assertTrue($post->categories->contains($category1));
        $this->assertTrue($post->categories->contains($category2));

        // Verify Morph Map is working (database should store the key, not the class name)
        $this->assertDatabaseHas('site_post_categorizables', [
            'site_post_id' => $post->id,
            'categorizable_id' => $category1->id,
            'categorizable_type' => 'site_post_category',
        ]);
    }

    public function test_category_can_have_multiple_posts(): void
    {
        $company = \App\Modules\Identity\Models\Company::factory()->create();
        $site = \App\Modules\Websites\Models\Site::create([
            'company_id' => $company->id,
            'name' => 'Test Site',
            'status' => 'development',
        ]);

        $category = \App\Modules\Websites\Models\SitePostCategory::create([
            'site_id' => $site->id,
            'name' => 'Category Main',
            'slug' => 'cat-main',
        ]);

        $post1 = \App\Modules\Websites\Models\SitePost::create([
            'site_id' => $site->id,
            'title' => 'Test Post 1',
            'slug' => 'test-post-1',
            'content' => 'Content here',
        ]);

        $post2 = \App\Modules\Websites\Models\SitePost::create([
            'site_id' => $site->id,
            'title' => 'Test Post 2',
            'slug' => 'test-post-2',
            'content' => 'Content here',
        ]);

        $category->posts()->attach([$post1->id, $post2->id]);

        $this->assertCount(2, $category->posts);
        $this->assertTrue($category->posts->contains($post1));
        $this->assertTrue($category->posts->contains($post2));
    }
}
