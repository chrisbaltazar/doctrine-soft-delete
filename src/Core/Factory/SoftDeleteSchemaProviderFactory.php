<?php

declare(strict_types=1);

namespace Database\SoftDelete\Core\Factory;

use Database\SoftDelete\Engine\Mysql\MySQLSchemaDefiner;
use Database\SoftDelete\Doctrine\Schema\SoftDeleteSchemaProvider;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\Migrations\Provider\SchemaProvider;
use Doctrine\ORM\EntityManagerInterface;

final class SoftDeleteSchemaProviderFactory
{
    public function __invoke(EntityManagerInterface $entityManager): SchemaProvider
    {
        $platform = $entityManager->getConnection()->getDatabasePlatform();
        $databaseSchemaHandler = match (true) {
            $platform instanceof AbstractMySQLPlatform => new MySQLSchemaDefiner(),
            default => throw new \RuntimeException('SoftDeletes: Unsupported database platform')
        };

        return new SoftDeleteSchemaProvider($entityManager, $databaseSchemaHandler);
    }
}