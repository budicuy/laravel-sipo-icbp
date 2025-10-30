<?php

namespace App\Providers;

use App\Events\RekamMedisCreated;
use App\Events\RekamMedisUpdated;
use App\Events\RekamMedisDeleted;
use App\Events\RekamMedisEmergencyCreated;
use App\Events\RekamMedisEmergencyUpdated;
use App\Events\RekamMedisEmergencyDeleted;
use App\Listeners\KurangiStokObatListener;
use App\Listeners\AdjustStokObatListener;
use App\Listeners\KembalikanStokObatListener;
use App\Listeners\KurangiStokObatEmergencyListener;
use App\Listeners\AdjustStokObatEmergencyListener;
use App\Listeners\KembalikanStokObatEmergencyListener;
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

        // Regular Rekam Medis Events
        RekamMedisCreated::class => [
            KurangiStokObatListener::class,
        ],

        RekamMedisUpdated::class => [
            AdjustStokObatListener::class,
        ],

        RekamMedisDeleted::class => [
            KembalikanStokObatListener::class,
        ],

        // Emergency Rekam Medis Events
        RekamMedisEmergencyCreated::class => [
            KurangiStokObatEmergencyListener::class,
        ],

        RekamMedisEmergencyUpdated::class => [
            AdjustStokObatEmergencyListener::class,
        ],

        RekamMedisEmergencyDeleted::class => [
            KembalikanStokObatEmergencyListener::class,
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
