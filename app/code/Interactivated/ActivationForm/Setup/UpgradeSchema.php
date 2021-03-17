<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Interactivated\ActivationForm\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Upgrade the Partialpayment module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 1) {
            if (!$installer->tableExists('interactivated_activationform')) {
                $table = $installer->getConnection()->newTable($installer->getTable('interactivated_activationform'));
                $table->addColumn(
                    'request_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true],
                    'Request Id'
                )->addColumn(
                    'email',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['identity' => false, 'nullable' => false, 'primary' => false],
                    'Email'
                )->addColumn(
                    'firstname',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['identity' => false, 'nullable' => false, 'primary' => false],
                    'firstname'
                )->addColumn(
                    'lastname',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['identity' => false, 'nullable' => false, 'primary' => false],
                    'lastname'
                )->addColumn(
                    'company',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['identity' => false, 'nullable' => false, 'primary' => false],
                    'company'
                )->addColumn(
                    'order_number',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['identity' => false, 'nullable' => false, 'primary' => false],
                    'order_number'
                )->addColumn(
                    'sim_number',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['identity' => false, 'nullable' => false, 'primary' => false],
                    'sim_number'
                )->addColumn(
                    'notes',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['identity' => false, 'nullable' => false, 'primary' => false],
                    'notes'
                )
                    ->setComment('activation requests table');
                $installer->getConnection()->createTable($table);
            }
        }
        if (version_compare($context->getVersion(), '1.0.2') < 1) {
            $setup->getConnection()
                ->addColumn(
                    $installer->getTable('interactivated_activationform'),
                    'status',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'length' => '255',
                        'comment' => 'status',
                        'default' => 1
                    ]
                );
        }
        if (version_compare($context->getVersion(), '1.0.3') < 1) {
            $setup->getConnection()
                ->addColumn(
                    $installer->getTable('interactivated_activationform'),
                    'completed_date',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                        'nullable' => true,
                        'comment' => 'completed_date',
                    ]
                );
        }
        if (version_compare($context->getVersion(), '1.0.4') < 1) {
            $setup->getConnection()
                ->addColumn(
                    $installer->getTable('interactivated_activationform'),
                    'expiration_date',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                        'nullable' => true,
                        'comment' => 'completed_date',
                    ]
                );
        }
        if (version_compare($context->getVersion(), '1.0.5') < 1) {
            $setup->getConnection()
                ->addColumn(
                    $installer->getTable('interactivated_activationform'),
                    'phone_number',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'phone_number',
                        'length' => '255',
                    ]
                );
        }
        if (version_compare($context->getVersion(), '1.0.6') < 1) {
            $setup->getConnection()
                ->addColumn(
                    $installer->getTable('interactivated_activationform'),
                    'comments',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'comments',
                        'length' => '255',
                    ]
                );
        }
        if (version_compare($context->getVersion(), '1.0.7') < 1) {
            $setup->getConnection()
                ->addColumn(
                    $installer->getTable('interactivated_activationform'),
                    'data_number',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'data_number',
                        'length' => '255',
                    ]
                );
        }
        if (version_compare($context->getVersion(), '1.0.8') < 1) {
            $setup->getConnection()
                ->addColumn(
                    $installer->getTable('interactivated_activationform'),
                    'desired_activation_date',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                        'nullable' => true,
                        'comment' => 'desired_activation_date',
                    ]
                );
        }
        $setup->endSetup();
    }
}
