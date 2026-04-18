<?php

declare(strict_types=1);

namespace Database\SoftDelete\Core\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class SoftDeleteUniqueIndex
{
    public function __construct(
        public readonly array $fields = [],
    ) {}
}