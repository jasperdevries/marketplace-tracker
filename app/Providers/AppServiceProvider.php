<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use ReflectionClass;
use function array_map;
use function implode;
use function is_object;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Str::macro('implodeFunctionArguments', function (array $arguments, string $separator = ':') {
            return implode(
                $separator,
                array_map(
                    fn($argument) => match (true) {
                        is_object($argument) && (new ReflectionClass($argument))->isEnum() => $argument->value,
                        $argument instanceof Model                                         => $argument->getKey(),
                        default                                                            => $argument,
                    },
                    $arguments
                )
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
