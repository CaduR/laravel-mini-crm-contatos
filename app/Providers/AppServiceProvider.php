<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Contacts\Repositories\ContactRepositoryInterface;
use App\Infrastructure\Persistence\Repositories\ContactEloquentRepository;


class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->bind (
            ContactRepositoryInterface::class,
            ContactEloquentRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
