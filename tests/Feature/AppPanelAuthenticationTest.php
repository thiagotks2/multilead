<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppPanelAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_user_in_active_company_can_access_app_panel(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'company_id' => $company->id,
            'active' => true,
        ]);

        $response = $this->actingAs($user, 'user')->get('/app/' . $company->id);
        $response->assertStatus(200);
    }

    public function test_inactive_user_cannot_access_app_panel(): void
    {
        $company = Company::factory()->create(['active' => true]);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'active' => false,
        ]);

        $response = $this->actingAs($user, 'user')->get('/app');
        $response->assertStatus(403);
    }

    public function test_active_user_in_inactive_company_cannot_access_app_panel(): void
    {
        $company = Company::factory()->create(['active' => false]);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'active' => true,
        ]);

        $response = $this->actingAs($user, 'user')->get('/app');
        $response->assertStatus(403);
    }

    public function test_user_cannot_access_admin_panel(): void
    {
        $company = Company::factory()->create(['active' => true]);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'active' => true,
        ]);

        $this->assertFalse($user->canAccessPanel(app(\Filament\FilamentManager::class)->getPanel('admin')));
    }
}
