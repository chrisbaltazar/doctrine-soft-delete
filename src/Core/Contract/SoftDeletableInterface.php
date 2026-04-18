<?php

declare(strict_types=1);

namespace Database\SoftDelete\Core\Contract;

interface SoftDeletableInterface
{
    public function setDeletedAt(\DateTimeImmutable $deletedAt);

    public function getDeletedAt(): ?\DateTimeImmutable;
}