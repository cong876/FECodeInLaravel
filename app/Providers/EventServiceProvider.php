<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\SendEmail' => [
            'App\Listeners\SendEmailListener',
        ],
        'App\Events\MailToSellerForDeliverEvent' => [
            'App\Listeners\MailToSellerForDeliverListener'
        ],
        'App\Events\SavePictureToQN' => [
            'App\Listeners\SavePictureToQNListener'
        ],
        'App\Events\RequirementNotification' => [
            'App\Listeners\RequirementNotificationListener'
        ],
        'App\Events\DeliveryNotification' => [
            'App\Listeners\DeliveryNotificationListener'
        ],
        'App\Events\RemindUserWithWX' => [
            'App\Listeners\RemindUserWithWXListener'
        ],
        'App\Events\UserTalkedThroughWeChat' => [
            'App\Listeners\BroadcastToOperator'
        ]
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
