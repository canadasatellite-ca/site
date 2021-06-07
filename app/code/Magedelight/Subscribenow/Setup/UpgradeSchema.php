<?php

/**
 * Magedelight
 * Copyright (C) 2017 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Subscribenow
 * @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Subscribenow\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magedelight\Subscribenow\Model\Subscription;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    const TBL_PRODUCT_SUBSCRIBER_BACKUP = 'md_subscribenow_product_subscribers_100x1x3';
    
    const TBL_FUTUREPRODUCTS_DAILY = 'md_subscribenow_futureproducts_aggregated_daily';
    const TBL_FUTUREPRODUCTS_MONTHLY = 'md_subscribenow_futureproducts_aggregated_monthly';
    const TBL_FUTUREPRODUCTS_YEARLY = 'md_subscribenow_futureproducts_aggregated_yearly';
    
    /**
     * @var \Magento\Framework\Setup\InstallSchemaInterface
     */
    private $installer = null;
    
    /**
     * @var Connection
     */
    private $connection = null;

    /**
     * @var Context
     */
    private $version = null;
    
    /**
     * Set Default Setup Variable
     * @return void
     */
    private function clearSetupVars()
    {
        $this->installer = null;
        $this->connection = null;
        $this->version = null;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->installer = $setup;
        $this->connection = $this->installer->getConnection();
        
        $this->installer->startSetup();
        $this->version = $context->getVersion();
        
        if($context->getVersion())
        {
            if (version_compare($context->getVersion(), '200.0.0') < 0) {
                $this->executeBeforeUpgrade($setup);
            }
        }
        
        // Table Operations
        $this->setupSubscriptionHistory();
        $this->alterProductSubscriber();
        $this->alterSalesOrder();
        
        if (version_compare($context->getVersion(), '200.0.2', '<')) {
            $this->createTblFutureProductAggregated($setup);
        }
        
        $this->installer->endSetup();
        $this->clearSetupVars();
    }
    
    /**
     * Create Subscription History Table
     * table `md_subscribenow_product_subscription_history`
     *
     * @return void
     */
    private function setupSubscriptionHistory()
    {
        $tableName = $this->installer->getTable(Subscription::TBL_SUBSCRIPTION_HISTORY);
        $table = $this->connection->newTable($tableName);
                
        $table->addColumn(
            'hid',
            Table::TYPE_INTEGER,
            11,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'History id'
        )->addColumn(
            'subscription_id',
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false],
            'Product Subscription id'
        )->addColumn(
            'modify_by',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Product Subscription Affect By'
        )->addColumn(
            'comment',
            Table::TYPE_TEXT,
            null,
            [],
            'Product Subscription Comment'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Subscription created at'
        )->addForeignKey(
            Subscription::TBL_SUBSCRIPTION_HISTORY . '_subscription_id',
            'subscription_id',
            $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER),
            'subscription_id',
            Table::ACTION_CASCADE
        );

        $this->connection->createTable($table);
    }
    
    /**
     Alter Product Subscriber Table
     * @return void
     */
    private function alterProductSubscriber()
    {
        if (version_compare($this->version, '100.1.1', '<')) {
            $this->connection->addColumn(
                $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER),
                'next_occurrence_date',
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT
                ],
                'Next Occurence Date'
            );
        }

        if (version_compare($this->version, '100.1.4', '<')) {
            $this->connection->addColumn(
                $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER), 'base_currency_code', [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => 3,
                    'comment' => 'Base Currency Code'
                ]
            );

            $this->connection->addColumn(
                $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER), 'base_billing_amount', [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'default' => 0,
                    'length' => '10,4',
                    'comment' => 'Base Billing Amount'
                ]
            );

            $this->connection->addColumn(
                $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER), 'base_trial_billing_amount', [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'default' => 0,
                    'length' => '10,4',
                    'comment' => 'Base Trial Billing Amount'
                ]
            );

            $this->connection->addColumn(
                $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER), 'base_initial_amount', [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'default' => 0,
                    'length' => '10,4',
                    'comment' => 'Base Initial Amount'
                ]
            );
        }
        
        if (version_compare($this->version, '200.0.0', '<')) {
            
            $this->connection->addColumn(
                $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER),
                'last_bill_date',
                [
                    'type' => Table::TYPE_TIMESTAMP,
                    'nullable' => true,
                    'comment' => 'Last Billing Paid Date'
                ]
            );
            
            $this->connection->addColumn(
                $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER),
                'trial_count',
                [
                    'type' => Table::TYPE_INTEGER,
                    'default' => 0,
                    'comment' => 'Trial Count'
                ]
            );
            
            $this->connection->addColumn(
                $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER),
                'is_trial',
                [
                    'type' => Table::TYPE_BOOLEAN,
                    'default' => 0,
                    'comment' => 'Is Trial'
                ]
            );
            
            $this->connection->addColumn(
                $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER),
                'shipping_method_code',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => 255,
                    'comment' => 'Shipping Method Code'
                ]
            );
            
            $this->connection->addColumn(
                $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER),
                'payment_token',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Payment Token'
                ]
            );
            
            $this->connection->addColumn(
                $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER),
                'total_bill_count',
                [
                    'type' => Table::TYPE_INTEGER,
                    'default' => 0,
                    'comment' => 'Total Bill Count'
                ]
            );
            
            $this->connection->addColumn(
                $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER),
                'base_shipping_amount',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'default' => 0,
                    'length' => '10,4',
                    'comment' => 'Base Shipping Amount'
                ]
            );
            
            $this->connection->addColumn(
                $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER),
                'base_tax_amount',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'default' => 0,
                    'length' => '10,4',
                    'comment' => 'Base Tax Amount'
                ]
            );
            
            $this->connection->addColumn(
                $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER),
                'base_discount_amount',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'default' => 0,
                    'length' => '10,4',
                    'comment' => 'Base Discount Amount'
                ]
            );
            
            $this->connection->addColumn(
                $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER),
                'initial_order_id',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => false,
                    'length' => '32',
                    'comment' => 'Initial Order ID'
                ]
            );
            
            $this->connection->addColumn(
                $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER),
                'billing_address_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'length' => '11',
                    'comment' => 'Billing Address ID'
                ]
            );
            
            $this->connection->addColumn(
                $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER),
                'shipping_address_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'length' => '11',
                    'comment' => 'Shipping Address ID'
                ]
            );
            
            $this->connection->changeColumn(
                $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER),
                'next_occurrence_date',
                'next_occurrence_date',
                ['type' => Table::TYPE_DATETIME,'nullable' => false,'comment' => "Next Occurence Date"]
            );
            
            $this->connection->changeColumn(
                $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER),
                'created_at',
                'created_at',
                ['type' => Table::TYPE_DATETIME,'nullable' => false,'comment' => "Subscription created at"]
            );
            
            $this->connection->changeColumn(
                $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER),
                'updated_at',
                'updated_at',
                ['type' => Table::TYPE_DATETIME,'nullable' => false,'comment' => "Subscription updated at"]
            );
            
            $this->connection->changeColumn(
                $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER),
                'subscription_start_date',
                'subscription_start_date',
                ['type' => Table::TYPE_DATETIME,'nullable' => false,'comment' => "Subscription Start Date"]
            );
            
            $this->connection->changeColumn(
                $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER),
                'last_bill_date',
                'last_bill_date',
                ['type' => Table::TYPE_DATETIME,'nullable' => true,'comment' => "Last Billing Paid Date"]
            );
        }
        
        // 200.0.1 to 200.0.2
        if (version_compare($this->version, '200.0.2', '<')) {
            
            $this->connection->addColumn(
                $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER),
                'product_name',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Product Name'
                ]
            );
            $this->connection->addColumn(
                $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER),
                'payment_title',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Payment Method Title'
                ]
            );
        }
    }
    
    private function alterSalesOrder()
    {
        if (version_compare($this->version, '100.1.3', '<')) {
            $this->connection->addColumn(
                $this->installer->getTable('sales_order'),
                'subscription_parent_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'length' => '11',
                    'comment' => 'Parent Subscription ID'
                ]
            );
        }
    }
    
    private function executeBeforeUpgrade($setup)
    {
        $table = $setup->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER);
        
        $setup->getConnection()->changeColumn(
            $table,
            'next_occurrence_date',
            'next_occurrence_date',
            [
                'type'    => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                'default' => null
            ]
        );
        
        $backup_table = $setup->getTable(self::TBL_PRODUCT_SUBSCRIBER_BACKUP);

        $setup->getConnection()->query("CREATE TABLE `$backup_table` LIKE `$table`");
        $setup->getConnection()->query("INSERT `$backup_table` SELECT * FROM `$table`");
    }
    
    private function createTblFutureProductAggregated($installer)
    {
    	$tablesToCreate = [
    		'daily' => self::TBL_FUTUREPRODUCTS_DAILY,
    		'monthly' => self::TBL_FUTUREPRODUCTS_MONTHLY,
    		'yearly' => self::TBL_FUTUREPRODUCTS_YEARLY
    	];

    	foreach($tablesToCreate as $key => $tbl)
    	{
    		$table = $installer->getConnection()->newTable(
	            $installer->getTable($tbl)
	        )->addColumn(
	            'id',
	            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
	            null,
	            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
	            'Id'
	        )->addColumn(
	            'period',
	            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
	            null,
	            [],
	            'Period'
	        )->addColumn(
	            'store_id',
	            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
	            null,
	            ['unsigned' => true],
	            'Store Id'
	        )->addColumn(
	            'product_id',
	            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
	            null,
	            ['unsigned' => true],
	            'Product Id'
	        )->addColumn(
	            'product_sku',
	            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
	            255,
	            ['nullable' => true],
	            'Product SKU'
	        )->addColumn(
	            'product_name',
	            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
	            255,
	            ['nullable' => true],
	            'Product Name'
	        )->addColumn(
	            'qty_ordered',
	            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
	            '12,4',
	            ['nullable' => false, 'default' => '0.0000'],
	            'Qty Ordered'
	        )->addColumn(
	            'subscription_count',
	            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
	            '12,4',
	            ['nullable' => false, 'default' => '0.0000'],
	            'Active Subscription Count'
	        )->addIndex(
	            $installer->getIdxName(
	                $tbl,
	                ['period', 'store_id', 'product_id'],
	                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
	            ),
	            ['period', 'store_id', 'product_id'],
	            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
	        )->addIndex(
	            $installer->getIdxName($tbl, ['store_id']),
	            ['store_id']
	        )->addIndex(
	            $installer->getIdxName($tbl, ['product_id']),
	            ['product_id']
	        )->addForeignKey(
	            $installer->getFkName($tbl, 'store_id', 'store', 'store_id'),
	            'store_id',
	            $installer->getTable('store'),
	            'store_id',
	            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
	        )->setComment(
	            'Subscribenow Future Product Aggregated '.ucfirst($key)
	        );

	        $installer->getConnection()->createTable($table);
    	}
    }
}
