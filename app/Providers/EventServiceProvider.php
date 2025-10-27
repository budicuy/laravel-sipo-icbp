<?php

namespace App\Providers;

use App\Events\RekamMedisCreated;
use App\Events\RekamMedisUpdated;
use App\Events\RekamMedisDeleted;
use App\Listeners\KurangiStokObatListener;
use App\Listeners\AdjustStokObatListener;
use App\Listeners\KembalikanStokObatListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        RekamMedisCreated::class => [
            KurangiStokObatListener::class,
        ],

        RekamMedisUpdated::class => [
            AdjustStokObatListener::class,
        ],

        RekamMedisDeleted::class => [
            KembalikanStokObatListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
