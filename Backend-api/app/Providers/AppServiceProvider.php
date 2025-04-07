<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\API\V1\Contracts\{ContactRepositoryInterface,UserRepositoryInterface};
use App\Repositories\API\V1\Repository\{ContactRepository,UserRepository};
use App\Services\API\V1\Contracts\{ContactServiceInterface,UserServiceInterface};
use App\Services\API\V1\Service\{ContactService,UserService};

use App\Repositories\API\V1\Contracts\Industry\{IndustryRepositoryInterface};
use App\Repositories\API\V1\Repository\Industry\{IndustryRepository};
use App\Services\API\V1\Contracts\Industry\{IndustryServiceInterface};
use App\Services\API\V1\Service\Industry\{IndustryService};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Binding the UserInterfaces to UserRepository
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);

        // Binding the ContactInterfaces to ContactRepository
        $this->app->bind(ContactRepositoryInterface::class, ContactRepository::class);
        $this->app->bind(ContactServiceInterface::class, ContactService::class);

        // Binding the IndustryInterfaces to ContactRepository
        $this->app->bind(IndustryRepositoryInterface::class, IndustryRepository::class);
        $this->app->bind(IndustryServiceInterface::class, IndustryService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}