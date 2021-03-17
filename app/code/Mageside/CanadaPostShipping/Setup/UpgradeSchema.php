<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;


class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.3.0', '<')) {
            $connection = $setup->getConnection();
            $connection->addColumn(
                $setup->getTable('mageside_canadapost_shipment'),
                'cost',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable'  => true,
                    'length'    => '5,2',
                    'comment'   => 'Cost',
                    'after'     => 'status'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.5.0', '<')) {
            $tableName = $setup->getTable('mageside_canadapost_logs');
            if (!$setup->getConnection()->isTableExists($tableName)) {
                $table = $setup->getConnection()
                    ->newTable($setup->getTable('mageside_canadapost_logs'))
                    ->addColumn(
                        'record_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                        'ID'
                    )
                    ->addColumn(
                        'call',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        50,
                        ['nullable' => true],
                        'Call'
                    )
                    ->addColumn(
                        'request',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        null,
                        ['nullable' => true],
                        'Request'
                    )
                    ->addColumn(
                        'response',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        null,
                        ['nullable' => true],
                        'Response'
                    )
                    ->addColumn(
                        'status',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        50,
                        ['nullable' => true],
                        'Status'
                    )
                    ->addColumn(
                        'created_at',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                        'Creation Time'
                    )
                    ->setComment('Canadapost Requests Logs Table');
                $setup->getConnection()->createTable($table);
            }
        }

        $setup->endSetup();
    }
}
