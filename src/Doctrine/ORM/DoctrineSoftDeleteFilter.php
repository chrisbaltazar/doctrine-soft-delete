<?php

declare(strict_types=1);

namespace Database\SoftDelete\Doctrine\ORM;

use Database\SoftDelete\Core\Contract\SoftDeletableInterface;
use Database\SoftDelete\Core\SoftDeleteNames;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Doctrine\Persistence\Mapping\ClassMetadata;

class DoctrineSoftDeleteFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, string $targetTableAlias): string
    {
        if (!$targetEntity->getReflectionClass()?->implementsInterface(SoftDeletableInterface::class)) {
            return '';
        }

        return sprintf('%s.%s IS NULL', $targetTableAlias, SoftDeleteNames::SOFT_DELETE_COLUMN_NAME);
    }
}