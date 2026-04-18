<?php

declare(strict_types=1);

namespace Database\SoftDelete\Doctrine\Schema;

use Database\SoftDelete\Engine\Mysql\MySQLSchemaDefiner;
use Database\SoftDelete\Engine\Mysql\SoftDeleteMySQLComparator;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\MySQLSchemaManager;

class SoftDeleteMySQLSchemaManager extends MySQLSchemaManager
{
    public function __construct(
        Connection $connection,
        AbstractPlatform $platform,
        private readonly MySQLSchemaManager $inner,
    ) {
        parent::__construct($connection, $platform);
    }

    public function createComparator(): Comparator
    {
        /** @var \Doctrine\DBAL\Platforms\MySQL\Comparator $comparator */
        $comparator = $this->inner->createComparator();

        return new SoftDeleteMySQLComparator($this->platform, $comparator, new MySQLSchemaDefiner());
    }
}