<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Report;
use App\Models\User;
use App\Observers\ReportObserver;
use App\Observers\UserObserver;

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
        // ---- Koordinator ----
        Gate::define(
            'manage-roles',
            fn(User $user): bool => $user->role === 'koordinator'
        );
        Gate::define(
            'manage-master-data',
            fn(User $user): bool => $user->role === 'koordinator'
        );
        Gate::define(
            'forward-report',
            fn(User $user): bool => $user->role === 'koordinator'
        );

        // ---- Pemantau Komunitas ----
        Gate::define(
            'review-report',
            fn(User $user): bool => $user->role === 'pemantau'
        );

        // ---- Pemantau & Koordinator ----
        Gate::define(
            'view-recap',
            fn(User $user): bool => in_array(
                $user->role,
                ['pemantau', 'koordinator'],
                true
            )
        );

        // ---- Pelapor ----
        Gate::define(
            'create-report',
            fn(User $user): bool => $user->role === 'pelapor'
        );

        // ---- Berbasis ownership ----
        Gate::define(
            'view-report',
            fn(User $user, Report $report): bool =>
                $user->role === 'pelapor'
                    ? $user->id_user === $report->id_user
                    : in_array($user->role, ['pemantau', 'koordinator'], true)
        );
        Gate::define(
            'update-report',
            fn(User $user, Report $report): bool =>
                $user->role === 'pelapor'
                    && $user->id_user === $report->id_user
                    && $report->isEditable()
        );

        User::observe(UserObserver::class);
        Report::observe(ReportObserver::class);
    }
}