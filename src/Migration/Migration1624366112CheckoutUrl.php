<?php declare(strict_types=1);

namespace Kiener\MolliePayments\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1624366112CheckoutUrl extends MigrationStep
{

    /**
     * @return int
     */
    public function getCreationTimestamp(): int
    {
        return 1624366112;
    }

    /**
     * @param Connection $connection
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Exception
     */
    public function update(Connection $connection): void
    {
        $sql = <<<SQL
            CREATE TABLE IF NOT EXISTS `mollie_checkout` (
                `id` BINARY(16) NOT NULL,
                `transaction_id` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
                `finalize_url` LONGTEXT COLLATE utf8mb4_unicode_ci,
                `created_at` DATETIME(3) NOT NULL,
                PRIMARY KEY (`id`)
            )
                ENGINE = InnoDB
                DEFAULT CHARSET = utf8mb4
                COLLATE = utf8mb4_unicode_ci;
            SQL;

        $connection->executeUpdate($sql);
    }

    /**
     * @param Connection $connection
     */
    public function updateDestructive(Connection $connection): void
    {
    }

}
