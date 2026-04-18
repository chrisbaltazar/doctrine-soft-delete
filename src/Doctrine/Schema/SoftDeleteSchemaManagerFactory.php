<?php

declare(strict_types=1);

namespace Database\SoftDelete\Doctrine\Schema;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\SchemaManagerFactory;

class SoftDeleteSchemaManagerFactory implements SchemaManagerFactory
{
    public function createSchemaManager(Connection $connection): AbstractSchemaManager
    {
        $platform = $connection->getDatabasePlatform();
        $schemaManager = $platform->createSchemaManager($connection);

        return match (true) {
            $platform instanceof AbstractMySQLPlatform => new SoftDeleteMySQLSchemaManager(
                $connection,
                $platform,
                $schemaManager,
            ),
            default => $schemaManager,
        };
    }
}