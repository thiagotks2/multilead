<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Company;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanelIsolationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * BR01 (Wall - Admin to App)
     */
    public function test_admin_user_cannot_access_app_panel(): void
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        $appPanel = Filament::getPanel('app');
        $this->assertFalse($admin->canAccessPanel($appPanel));
    }

    /**
     * BR02 (Wall - User to Admin)
     */
    public function test_standard_user_cannot_access_admin_panel(): void
    {
        $company = Company::factory()->create(['active' => true]);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'active' => true,
        ]);
        $this->actingAs($user, 'user');

        $adminPanel = Filament::getPanel('admin');
        $this->assertFalse($user->canAccessPanel($adminPanel));
    }
}
