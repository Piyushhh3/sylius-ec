<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

final class Version20240315112656 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Replace DC2TYPE:array with JSON';
    }

    public function up(Schema $schema): void
    {
        foreach ($this->tablesAndColumnsToBeUpdated() as [$table, $column]) {
            $this->changeTypesFromLongtextToJsonAndEncodeSerializedData($table, $column);
        }
    }

    public function down(Schema $schema): void
    {
        foreach ($this->tablesAndColumnsToBeUpdated() as [$table, $column]) {
            $this->changeTypesFromJsonToLongtext($table, $column);
        }
    }

    public function postDown(Schema $schema): void
    {
        foreach ($this->tablesAndColumnsToBeUpdated() as [$table, $column]) {
            $this->serializeEncodedData($table, $column);
        }
    }

    private function changeTypesFromLongtextToJsonAndEncodeSerializedData(string $table, string $dataColumn): void
    {
        if ($table === 'sylius_address_log_entries') {
            $this->addSql(sprintf('ALTER TABLE %s CHANGE %s %s JSON DEFAULT NULL', $table, $dataColumn, $dataColumn));
        } else {
            $this->addSql(sprintf('ALTER TABLE %s CHANGE %s %s JSON NOT NULL', $table, $dataColumn, $dataColumn));
        }

        $connection = $this->connection;
        $rows = $connection->fetchAllAssociative(sprintf('SELECT %s, %s FROM %s', 'id', $dataColumn, $table));

        foreach ($rows as $row) {
            $id = $row['id'];
            $data = $row[$dataColumn];

            $this->skipIf(@unserialize($data) === false, sprintf('Data in %s is not serialized', $table));
            $encodedData = unserialize($data);
            $encodedData = json_encode($encodedData);

            $connection->UPDATE($table, [$dataColumn => $encodedData], ['id' => $id]);
        }
    }

    private function changeTypesFromJsonToLongtext(string $table, string $dataColumn): void
    {
        $this->addSql(sprintf('ALTER TABLE %s CHANGE %s %s LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'', $table, $dataColumn, $dataColumn));
    }

    private function serializeEncodedData(string $table, string $dataColumn): void
    {
        $connection = $this->connection;
        $rows = $connection->fetchAllAssociative(sprintf('SELECT %s, %s FROM %s', 'id', $dataColumn, $table));

        foreach ($rows as $row) {
            $id = $row['id'];
            $data = $row[$dataColumn];

            $decodedData = json_decode($data, true);
            $this->skipIf(json_last_error() !== JSON_ERROR_NONE, sprintf('Data in %s is not json', $table));
            $decodedData = serialize($decodedData);

            $connection->UPDATE($table, [$dataColumn => $decodedData], ['id' => $id]);
        }
    }

    /**
     * @return iterable<array{string, string}>
     */
    private function tablesAndColumnsToBeUpdated(): iterable
    {
        yield ['sylius_address_log_entries', 'data'];
        yield ['sylius_admin_user', 'roles'];
        yield ['sylius_catalog_promotion_action', 'configuration'];
        yield ['sylius_catalog_promotion_scope', 'configuration'];
        yield ['sylius_product_attribute', 'configuration'];
        yield ['sylius_promotion_action', 'configuration'];
        yield ['sylius_promotion_rule', 'configuration'];
        yield ['sylius_shipping_method', 'configuration'];
        yield ['sylius_shipping_method_rule', 'configuration'];
        yield ['sylius_shop_user', 'roles'];
    }
}
