<?php
namespace CanadaSatellite\Theme\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    function upgrade( SchemaSetupInterface $setup, ModuleContextInterface $context ) {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {

            $installer->getConnection()->addColumn(
                $installer->getTable('mageworx_downloads_attachment'),
                'sort_order_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 10,
                    'nullable' => false,
                    'comment' => 'Sort Order Id',
                    'after' => 'attachment_id'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {

            $installer->getConnection()->addColumn(
                $installer->getTable('mageworx_downloads_attachment'),
                'is_visible_top',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Is Visible On Top'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.6', '<')) {

            $installer->getConnection()->addColumn(
                $installer->getTable('mageworx_optiontemplates_group_option'),
                'currency_code',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'Currency Code'
                ]
            );

            $installer->getConnection()->addColumn(
                $installer->getTable('catalog_product_option'),
                'currency_code',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'Currency Code'
                ]
            );
        }

        $installer->endSetup();
    }
}