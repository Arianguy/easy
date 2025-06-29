<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // CRM Routes
    Route::get('customers', App\Livewire\Customers\CustomersList::class)->name('customers.index');
    Route::get('customers/create', App\Livewire\Customers\CustomerForm::class)->name('customers.create');
    Route::get('customers/{customer}/edit', App\Livewire\Customers\CustomerForm::class)->name('customers.edit');

    Route::get('leads', App\Livewire\Leads\LeadsList::class)->name('leads.index');
    Route::get('leads/create', App\Livewire\Leads\LeadForm::class)->name('leads.create');
    Route::get('leads/{lead}/edit', App\Livewire\Leads\LeadForm::class)->name('leads.edit');

    Route::get('opportunities', App\Livewire\Opportunities\OpportunitiesList::class)->name('opportunities.index');
    Route::get('opportunities/create', App\Livewire\Opportunities\OpportunityForm::class)->name('opportunities.create');
    Route::get('opportunities/{opportunity}/edit', App\Livewire\Opportunities\OpportunityForm::class)->name('opportunities.edit');

    Route::get('campaigns', App\Livewire\Campaigns\CampaignsList::class)->name('campaigns.index');
    Route::get('campaigns/create', App\Livewire\Campaigns\CampaignForm::class)->name('campaigns.create');
    Route::get('campaigns/{campaign}/edit', App\Livewire\Campaigns\CampaignForm::class)->name('campaigns.edit');

    Route::get('activities', App\Livewire\Activities\ActivitiesList::class)->name('activities.index');
    Route::get('activities/create', App\Livewire\Activities\ActivityForm::class)->name('activities.create');
    Route::get('activities/{activity}/edit', App\Livewire\Activities\ActivityForm::class)->name('activities.edit');

    // Management Routes (for Area Managers)
    Route::middleware(['role:Area Manager'])->group(function () {
        Route::get('branches', App\Livewire\Management\BranchesList::class)->name('branches.index');
        Route::get('branches/create', App\Livewire\Management\BranchForm::class)->name('branches.create');
        Route::get('branches/{branch}/edit', App\Livewire\Management\BranchForm::class)->name('branches.edit');

        Route::get('users', App\Livewire\Management\UsersList::class)->name('users.index');
        Route::get('users/create', App\Livewire\Management\UserForm::class)->name('users.create');
        Route::get('users/{user}/edit', App\Livewire\Management\UserForm::class)->name('users.edit');

        Route::get('permissions', App\Livewire\Management\PermissionsList::class)->name('permissions.index');
        Route::get('permissions/create', App\Livewire\Management\PermissionForm::class)->name('permissions.create');
        Route::get('permissions/{permission}/edit', App\Livewire\Management\PermissionForm::class)->name('permissions.edit');
    });
});

require __DIR__ . '/auth.php';
