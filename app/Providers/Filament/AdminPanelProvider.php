<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationItem;
use App\Filament\Resources\PostResource;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->sidebarCollapsibleOnDesktop()
            ->login()
            ->brandLogo(asset('/kone-transparent.png'))
            ->brandLogoHeight('5rem')
            ->colors([
                'danger' => '#EF4444',  // Soft Red
                'gray' => '#1E1E1E',    // Dark Mode Background
                'info' => '#3B82F6',    // Sky Blue (Info color)
                'primary' => '#6A0DFF', // Electric Purple
                'success' => '#10B981', // Emerald Green
                'warning' => '#4F46E5', // Indigo (for energetic look)
            ])
            ->favicon(asset('/kone-transparent.png'))
            ->navigationItems([
                NavigationItem::make('Add Post')
                    ->url('/admin/posts/create')
                    ->group('Posts')
                    ->sort(2),
                NavigationItem::make('Add Video')
                    ->url('/admin/videos/create')
                    ->icon('heroicon-o-plus-circle')
                    ->group('Videos')
                    ->sort(2),
                NavigationItem::make('Add Provider')
                    ->url('/admin/providers/create')
                    ->group('Providers')
                    ->sort(3),
                NavigationItem::make('Add Product')
                    ->url('/admin/products/create')
                    ->group('Providers')
                    ->sort(5)
            ])

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
