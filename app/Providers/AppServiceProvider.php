<?php

namespace App\Providers;

use App\Modules\Identity\Models\Company;
use App\Modules\Identity\Observers\CompanyObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Joaopaulolndev\FilamentEditProfile\Livewire\BrowserSessionsForm;
use Joaopaulolndev\FilamentEditProfile\Livewire\CustomFieldsForm;
use Joaopaulolndev\FilamentEditProfile\Livewire\DeleteAccountForm;
use Joaopaulolndev\FilamentEditProfile\Livewire\EditPasswordForm;
use Joaopaulolndev\FilamentEditProfile\Livewire\EditProfileForm;
use Joaopaulolndev\FilamentEditProfile\Livewire\MultiFactorAuthentication;
use Joaopaulolndev\FilamentEditProfile\Livewire\SanctumTokens;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'site_category' => \App\Modules\Websites\Models\SiteCategory::class,
        ]);

        Livewire::component('edit_profile_form', EditProfileForm::class);
        Livewire::component('edit_password_form', EditPasswordForm::class);
        Livewire::component('browser_sessions_form', BrowserSessionsForm::class);
        Livewire::component('delete_account_form', DeleteAccountForm::class);
        Livewire::component('custom_fields_form', CustomFieldsForm::class);
        Livewire::component('sanctum_tokens', SanctumTokens::class);
        Livewire::component('multi_factor_authentication', MultiFactorAuthentication::class);

        Company::observe(CompanyObserver::class);
    }
}
