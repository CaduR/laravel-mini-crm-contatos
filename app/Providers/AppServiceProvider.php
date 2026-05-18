<?php

namespace App\Providers;

use App\Domain\Contacts\Repositories\ContactRepositoryInterface;
use App\Events\ContactScoreProcessed;
use App\Infrastructure\Persistence\Repositories\ContactEloquentRepository;
use App\Listeners\LogContactScoreProcessedListener;
use App\Models\Contact;
use App\Observers\ContactObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

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
        Event::listen(ContactScoreProcessed::class, LogContactScoreProcessedListener::class);
    }
}
