<?php

namespace App\Providers;

use App\Models\Deposit;
use App\Models\Distribution;
use App\Models\Notification;
use App\Policies\DepositPolicy;
use App\Policies\DistributionPolicy;
use App\Policies\NotificationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Deposit::class => DepositPolicy::class,
        Distribution::class => DistributionPolicy::class,
        Notification::class => NotificationPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
