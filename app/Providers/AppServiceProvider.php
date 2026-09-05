<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Pengumuman; // Import Model
use App\Models\Tenant;
use App\Services\DokuService;
use App\Services\TenantProvisioningService;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register DOKU & Provisioning sebagai singleton (hemat resource)
        $this->app->singleton(DokuService::class);
        $this->app->singleton(TenantProvisioningService::class);
    }

    public function boot(): void
    {
        // View Composer: Mengirim data ke layout 'layouts.app'
        View::composer('layouts.app', function ($View) {
            $RecentAnnouncements = Pengumuman::latest()
                ->limit(5)
                ->get();
            $UnreadCount = $RecentAnnouncements->count();
            $View->with('RecentAnnouncements', $RecentAnnouncements);
            $View->with('UnreadCount', $UnreadCount);
        });

        // ✅ TAMBAHKAN INI: View Composer untuk alert subscription

        View::composer('*', function ($View) {
            $User = Auth::user();

            // Default values
            $CurrentTenant = null;
            $IsSubscriptionExpiringSoon = false;
            $SubscriptionRemainingDays = null;
            $IsSubscriptionExpired = false;

            if ($User && isset($User->tenant_id)) {
                // ✅ Gunakan first() untuk memastikan mendapatkan model instance
                $CurrentTenant = Tenant::where('Kode', $User->tenant_id)->first();
                if ($CurrentTenant) {
                    $IsSubscriptionExpiringSoon = $CurrentTenant->IsSubscriptionExpiringSoon(7);
                    $SubscriptionRemainingDays = $CurrentTenant->GetRemainingDays();
                    $IsSubscriptionExpired = $CurrentTenant->IsSubscriptionExpired();
                }
            }
            // dd($IsSubscriptionExpired);
            $View->with('CurrentTenant', $CurrentTenant);
            $View->with('IsSubscriptionExpiringSoon', $IsSubscriptionExpiringSoon);
            $View->with('SubscriptionRemainingDays', $SubscriptionRemainingDays);
            $View->with('IsSubscriptionExpired', $IsSubscriptionExpired);
        });
    }
}
