<?php

namespace App\Providers;

use App\Models\Contact;
use App\Observers\ContactObserver;
use Illuminate\Support\ServiceProvider;
use App\Domain\Contacts\Repositories\ContactRepositoryInterface;
use App\Infrastructure\Persistence\Repositories\ContactEloquentRepository;


class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->bind(
            ContactRepositoryInterface::class,
            ContactEloquentRepository::class
        );
    }

    public function boot(): void
    {
        Contact::observe(ContactObserver::class);
    }
}
