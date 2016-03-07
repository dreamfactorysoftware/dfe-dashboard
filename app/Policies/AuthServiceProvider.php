<?php namespace DreamFactory\Enterprise\Dashboard\Policies;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /** @inheritdoc */
    protected $policies = [
        'DreamFactory\Enterprise\Dashboard\Policies\Model' => 'App\Policies\ModelPolicy',
    ];

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Register any application authentication/authorization services.
     *
     * @param \Illuminate\Contracts\Auth\Access\Gate $gate
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);
    }
}
