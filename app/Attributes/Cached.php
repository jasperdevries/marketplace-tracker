<?php

declare(strict_types=1);

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Cached
{
    public function __construct(public int $ttl, public string $prefix = '', public ?string $key = null) {}
}
