<?php

declare(strict_types=1);

namespace Database\SoftDelete\Core;

final class SoftDeleteNames
{
    public const SOFT_DELETE_COLUMN_NAME = 'deleted_at';
    public const AUTO_GENERATED_COLUMN_NAME = 'auto_generated_active_flag';
    public const UNIQUE_INDEX_PREFIX = 'idx_soft_delete';
}