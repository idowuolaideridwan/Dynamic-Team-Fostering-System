<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\API\V1\Contracts\{UserRepositoryInterface};
use App\Repositories\API\V1\Repository\{UserRepository};
use App\Services\API\V1\Contracts\{UserServiceInterface};
use App\Services\API\V1\Service\{UserService};

use App\Repositories\API\V1\Contracts\Grade\{GradeRepositoryInterface};
use App\Repositories\API\V1\Repository\Grade\{GradeRepository};
use App\Services\API\V1\Contracts\Grade\{GradeServiceInterface};
use App\Services\API\V1\Service\Grade\{GradeService};

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

        // Binding the GradeInterfaces to GradeRepository
        $this->app->bind(GradeRepositoryInterface::class, GradeRepository::class);
        $this->app->bind(GradeServiceInterface::class, GradeService::class);
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