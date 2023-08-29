<?php

declare(strict_types=1);

namespace App\Traits;

use App\Attributes\Cached;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use ReflectionClass;
use function count;

trait HasCached
{
    public static function __callStatic($name, $arguments)
    {
        $reflection       = new ReflectionClass(self::class);
        $cachedAttributes = $reflection->getMethod($name)->getAttributes(Cached::class);
        if (count($cachedAttributes) === 0) {
            return self::$name(...$arguments);
        }

        $attributeInstance = $cachedAttributes[0]->newInstance();
        if (!is_null($attributeInstance->key)) {
            $key = $attributeInstance->key;
        } else {
            $key = $attributeInstance->prefix . self::class . ':' . $name . ':' . Str::implodeFunctionArguments($arguments);
        }

        return Cache::remember($key, $attributeInstance->ttl, function () use ($name, $arguments) {
            return self::$name(...$arguments);
        });
    }
}
