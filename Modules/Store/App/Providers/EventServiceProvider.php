<?php

namespace Modules\Store\App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Modules\Store\App\Observers\StoreObserver;
use ReflectionClass;
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     *
     * @return void
     */
    protected function configureEmailVerification(): void
    {


    }


     protected function observeModelsIn(string $path): void
    {
        $models = collect(File::allFiles($path))
            ->map(function ($file) {
                $path = $file->getRealPath();
                // Construct the class name from the file path
                $class = 'App\\' . Str::of($path)
                    ->after(app_path() . DIRECTORY_SEPARATOR)
                    ->replace(DIRECTORY_SEPARATOR, '\\')
                    ->before('.php');

                return (string) $class;
            })
            ->filter(function ($class) {
                try {
                    $reflection = new ReflectionClass($class);
                    // Check if it's a concrete class that extends Eloquent Model
                    return $reflection->isSubclassOf(\Illuminate\Database\Eloquent\Model::class) &&
                           !$reflection->isAbstract();
                } catch (\Throwable $e) {
                    return false;
                }
            });

        $models->each(function ($modelClass) {
            // Exclude models that should not be observed, if any.
            // For example, translation models.
            if (!Str::endsWith($modelClass, 'Translation')) {
                $modelClass::observe(StoreObserver::class);
            }
        });
    }
}
