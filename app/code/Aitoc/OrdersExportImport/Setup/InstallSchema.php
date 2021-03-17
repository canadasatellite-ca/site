<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\OrdersExportImport\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('aitoc_export_profile'))
            ->addColumn(
                'profile_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Profile ID'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store ID'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                '255',
                ['nullable' => false],
                'Name'
            )
            ->addColumn(
                'config',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Parameters'
            )
            ->addColumn(
                'date',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'Date'
            )
            ->addColumn(
                'flag_auto',
                Table::TYPE_SMALLINT,
                2,
                ['unsigned' => true, 'nullable' => true],
                'Flag'
            )
            ->addColumn(
                'crondate',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'Cron Date'
            )
            ->setComment('Export Profile');

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable($installer->getTable('aitoc_export'))
            ->addColumn(
                'export_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Export ID'
            )
            ->addColumn(
                'profile_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'Profile ID'
                ]
            )
            ->addColumn(
                'dt',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'DateTime'
            )
            ->addColumn(
                'filename',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'FileName'
            )
            ->addColumn(
                'serialized_config',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Parameters'
            )
            ->addColumn(
                'type_file',
                Table::TYPE_SMALLINT,
                3,
                ['unsigned' => true, 'nullable' => false],
                'Type file'
            )
            ->addColumn(
                'orders_count',
                Table::TYPE_BIGINT,
                8,
                ['unsigned' => true, 'nullable' => false],
                'Orders Count'
            )
            ->addColumn(
                'is_cron',
                Table::TYPE_SMALLINT,
                1,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Is Cron'
            )
            ->addColumn(
                'status',
                Table::TYPE_INTEGER,
                1,
                ['nullable' => false],
                'Status'
            )
            ->addForeignKey(
                $installer->getFkName(
                    'aitoc_export',
                    'profile_id',
                    'aitoc_export_profile',
                    'profile_id'
                ),
                'profile_id',
                $installer->getTable('aitoc_export_profile'),
                'profile_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Table Export');

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable($installer->getTable('aitoc_import'))
            ->addColumn(
                'import_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Import ID'
            )
            ->addColumn(
                'filename',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'FileName'
            )
            ->addColumn(
                'status',
                Table::TYPE_SMALLINT,
                5,
                ['nullable' => false, 'default' => 0],
                'Status'
            )
            ->addColumn(
                'serialized_config',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Parameters'
            )
            ->addColumn(
                'dt',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'DateTime'
            )
            ->addColumn(
                'error',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Error'
            )
            ->setComment('Table Import');

        $installer->getConnection()->createTable($table);

        $this->addStackTables($installer);

        $installer->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     *
     * @return $this
     */
    protected function addStackTables(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('aitoc_export_stack'))
            ->addColumn(
                'stack_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Stack ID'
            )
            ->addColumn(
                'export_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Export ID'
            )
            ->addColumn(
                'status',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Status'
            )
            ->addColumn(
                'error',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Error'
            )
            ->addColumn(
                'percent',
                Table::TYPE_TEXT,
                10,
                ['nullable' => false,'default' => 0],
                'Percent'
            )
            ->addColumn(
                'cron_date',
                Table::TYPE_DATETIME,
                10,
                ['nullable' => false],
                'Cron Date'
            )
            ->addForeignKey(
                $installer->getFkName(
                    'aitoc_export_stack',
                    'export_id',
                    'aitoc_export',
                    'export_id'
                ),
                'export_id',
                $installer->getTable('aitoc_export'),
                'export_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Export Stack');

        $installer->getConnection()->createTable($table);

        return $this;
    }
}
