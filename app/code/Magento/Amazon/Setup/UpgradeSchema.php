<?php

declare(strict_types=1);

namespace Magento\Amazon\Setup;

use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.10', '<')) {
            $this->removeUnusedColumnsFromActionTable($setup);
        }
        if (version_compare($context->getVersion(), '2.0.11', '<')) {
            $this->addProductTaxCodeColumns($setup);
        }
        if (version_compare($context->getVersion(), '2.0.12', '<')) {
            $this->makeOAuthColumnsNullableInAccountTable($setup);
            $this->addProductTaxCodeColumns($setup);
        }
        if (version_compare($context->getVersion(), '2.0.13', '<')) {
            $this->makeSellerIdColumnNullableInAccountTable($setup);
            $this->addIndexToUuidColumnInAccountTable($setup);
            $this->replaceSetupStepColumnWithAuthPendingColumnInAccountTable($setup);
        }
        if (version_compare($context->getVersion(), '2.0.14', '<')) {
            $this->addIndexToIdAndPurchaseDateColumnsInOrdersTable($setup);
        }
        if (version_compare($context->getVersion(), '2.0.15', '<')) {
            $this->addIndexToStatusColumnInOrdersTable($setup);
        }
        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function removeUnusedColumnsFromActionTable(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $actionTable = $setup->getTable('channel_amazon_action');
        $indexName = $connection->getIndexName($actionTable, ['uniquevalue'], AdapterInterface::INDEX_TYPE_UNIQUE);
        $connection->dropIndex($actionTable, $indexName);
        $connection->dropColumn($actionTable, 'api_group');
        $connection->dropColumn($actionTable, 'api_action');
        $connection->dropColumn($actionTable, 'api_content');
    }

    private function addProductTaxCodeColumns(SchemaSetupInterface $setup): void
    {
        $connection = $setup->getConnection();
        $accountListingTable = $setup->getTable('channel_amazon_account_listing');
        if (!$connection->tableColumnExists($accountListingTable, 'manage_ptc')) {
            $connection->addColumn(
                $accountListingTable,
                'manage_ptc',
                [
                    'type' => Table::TYPE_BOOLEAN,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Manage Product Tax Code'
                ]
            );
        }
        if (!$connection->tableColumnExists($accountListingTable, 'default_ptc')) {
            $connection->addColumn(
                $accountListingTable,
                'default_ptc',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'size' => 300,
                    'comment' => 'Default Product Tax Code'
                ]
            );
        }

        $listingTable = $setup->getTable('channel_amazon_listing');
        if (!$connection->tableColumnExists($accountListingTable, 'product_tax_code')) {
            $connection->addColumn(
                $listingTable,
                'product_tax_code',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'size' => 300,
                    'comment' => 'Product Tax Code'
                ]
            );
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function makeOAuthColumnsNullableInAccountTable(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $accountTable = $setup->getTable('channel_amazon_account');

        $connection->modifyColumn(
            $accountTable,
            'consumer_key',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => null,
                'comment' => 'Consumer Key'
            ]
        );

        $connection->modifyColumn(
            $accountTable,
            'consumer_secret',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => null,
                'comment' => 'Consumer Secret'
            ]
        );

        $connection->modifyColumn(
            $accountTable,
            'access_token',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => null,
                'comment' => 'Access Token'
            ]
        );

        $connection->modifyColumn(
            $accountTable,
            'access_secret',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => null,
                'comment' => 'Access Secret'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function makeSellerIdColumnNullableInAccountTable(SchemaSetupInterface $setup): void
    {
        $connection = $setup->getConnection();
        $accountTable = $setup->getTable('channel_amazon_account');

        $connection->modifyColumn(
            $accountTable,
            'seller_id',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => null,
                'comment' => 'Seller Id'
            ]
        );
    }

    private function addIndexToUuidColumnInAccountTable(SchemaSetupInterface $setup): void
    {
        $connection = $setup->getConnection();
        $accountTable = $setup->getTable('channel_amazon_account');

        $connection->addIndex(
            $accountTable,
            $connection->getIndexName($accountTable, ['uuid']),
            ['uuid']
        );
    }

    public function replaceSetupStepColumnWithAuthPendingColumnInAccountTable(SchemaSetupInterface $setup): void
    {
        $connection = $setup->getConnection();
        $accountTable = $setup->getTable('channel_amazon_account');
        $authPendingColumn = 'authentication_status';
        $connection->addColumn(
            $accountTable,
            $authPendingColumn,
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => false,
                'default' => Definitions::ACCOUNT_AUTH_STATUS_PENDING_AUTHENTICATION,
                'comment' => 'Authentication Status',
                'after' => 'is_active'
            ]
        );
        $connection->update(
            $accountTable,
            [
                $authPendingColumn => Definitions::ACCOUNT_AUTH_STATUS_AUTHENTICATED
            ],
            'seller_id IS NOT NULL AND seller_id != "" AND is_active != 2'
        );
        $connection->dropColumn(
            $accountTable,
            'setup_step'
        );
    }

    private function addIndexToIdAndPurchaseDateColumnsInOrdersTable(SchemaSetupInterface $setup): void
    {
        $connection = $setup->getConnection();
        $orderTable = $setup->getTable('channel_amazon_order');

        $connection->addIndex(
            $orderTable,
            $connection->getIndexName($orderTable, ['id', 'purchase_date']),
            ['id', 'purchase_date']
        );
    }

    private function addIndexToStatusColumnInOrdersTable(SchemaSetupInterface $setup): void
    {
        $connection = $setup->getConnection();
        $orderTable = $setup->getTable('channel_amazon_order');

        $connection->addIndex(
            $orderTable,
            $connection->getIndexName($orderTable, ['status']),
            ['status']
        );
    }
}
