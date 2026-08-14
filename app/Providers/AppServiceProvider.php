<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Pengumuman; // Import Model

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
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
    }
}
