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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magedelight\Subscribenow\Model\Subscription;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var \Magento\Framework\Setup\InstallSchemaInterface
     */
    private $installer = null;
    
    /**
     * @var Connection
     */
    private $connection = null;
    
    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->installer = $setup;
        $this->connection = $this->installer->getConnection();
        
        $this->installer->startSetup();
        
        // Create Table & Add Custom Attribute
        $this->setupProductSubscriber();
        $this->setupProductAssociatedOrders();
        $this->setupProductOccurrence();
        $this->setupAggregatedProduct();
        $this->setupAggregatedCustomer();
        $this->addCustomExtensionAttribute();
        
        $this->installer->endSetup();
        $this->installer = null;
        $this->connection = null;
    }

    /**
     * Product Subscription Table
     * create table `md_subscribenow_product_subscribers`
     *
     * @return void
     */
    private function setupProductSubscriber()
    {
        $getTable = $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER);
        
        $table = $this->connection->newTable($getTable);
                
        $table->addColumn(
            'subscription_id',
            Table::TYPE_INTEGER,
            11,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Unique id for subscribers'
        )->addColumn(
            'profile_id',
            Table::TYPE_TEXT,
            150,
            ['nullable' => false],
            'Subscription profile ID, Unique key'
        )->addIndex(
            $this->installer->getIdxName(
                $getTable,
                ['profile_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['profile_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addColumn(
            'customer_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Customer id of subscriber for website'
        )->addColumn(
            'product_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'Subscribed product id'
        )->addIndex(
            $this->installer->getIdxName(
                $getTable,
                ['product_id']
            ),
            ['product_id']
        )->addColumn(
            'subscriber_name',
            Table::TYPE_TEXT,
            150,
            ['nullable' => false],
            'Subscriber full name'
        )->addColumn(
            'subscriber_email',
            Table::TYPE_TEXT,
            150,
            ['nullable' => false],
            'Customer email adress'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store id from which subscriber has subscribed plan.'
        )->addIndex(
            $this->installer->getIdxName(
                $getTable,
                ['store_id']
            ),
            ['store_id']
        )->addColumn(
            'payment_method_code',
            Table::TYPE_TEXT,
            100,
            ['nullable' => false],
            'Payment Method code'
        )->addColumn(
            'subscription_start_date',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'subscription start date for subscriber'
        )->addColumn(
            'suspension_threshold',
            Table::TYPE_SMALLINT,
            5,
            ['nullable' => false],
            'limit for failure of payment till profile can active'
        )->addColumn(
            'billing_period_label',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Product Subscription billing period label'
        )->addColumn(
            'billing_period',
            Table::TYPE_SMALLINT,
            2,
            ['nullable' => false],
            'Product subscription billing period'
        )->addColumn(
            'billing_frequency',
            Table::TYPE_SMALLINT,
            5,
            ['nullable' => false],
            'Product subscription period frequency which defines once cycle'
        )->addColumn(
            'period_max_cycles',
            Table::TYPE_INTEGER,
            10,
            ['nullable' => false],
            'Subscription period max cycles to be repeated'
        )->addColumn(
            'billing_amount',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false],
            'Total billing amount for subscriber'
        )->addColumn(
            'trial_period_label',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Product Subscription billing period label'
        )->addColumn(
            'trial_period_unit',
            Table::TYPE_SMALLINT,
            6,
            ['nullable' => false],
            'Product subscription trial billing period'
        )->addColumn(
            'trial_period_frequency',
            Table::TYPE_SMALLINT,
            6,
            ['nullable' => false],
            'Product subscription trial period frequency'
        )->addColumn(
            'trial_period_max_cycle',
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false],
            'Product subscription trial period max cycles to be repeated'
        )->addColumn(
            'trial_billing_amount',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false],
            'Product subscription trial period billing amount'
        )->addColumn(
            'currency_code',
            Table::TYPE_TEXT,
            3,
            ['nullable' => false],
            'Subscription order currency code'
        )->addColumn(
            'shipping_amount',
            Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Subscription order shipping amount'
        )->addColumn(
            'tax_amount',
            Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Subscription order tax amount'
        )->addColumn(
            'initial_amount',
            Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Subscription order initial amount'
        )->addColumn(
            'discount_amount',
            Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Subscription order discount amount'
        )->addColumn(
            'order_info',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Subscription order info'
        )->addColumn(
            'order_item_info',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Order item info'
        )->addColumn(
            'billing_address_info',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Order billing information for customer'
        )->addColumn(
            'shipping_address_info',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Order shipping information for subscriber'
        )->addColumn(
            'additional_info',
            Table::TYPE_TEXT,
            null,
            [],
            'Subscriber related additional information'
        )->addColumn(
            'subscription_status',
            Table::TYPE_SMALLINT,
            6,
            ['nullable' => false],
            'Subscription status'
        )->addColumn(
            'initial_order',
            Table::TYPE_INTEGER,
            10,
            ['nullable' => false],
            'Initial Order Id'
        )->addColumn(
            'subscription_item_info',
            Table::TYPE_TEXT,
            null,
            [],
            'Subscriber Item Info'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Subscription created at'
        )->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Subscription updated at'
        )->addForeignKey(
            $getTable . '_store_id',
            'store_id',
            $this->installer->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $getTable . '_product_id',
            'product_id',
            $this->installer->getTable('catalog_product_entity'),
            'entity_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $getTable . '_customer_id',
            'customer_id',
            $this->installer->getTable('customer_entity'),
            'entity_id',
            Table::ACTION_CASCADE
        );
        
        $this->connection->createTable($table);
    }
    
    /**
     * Associate Product Order Table
     * create table `md_subscribenow_product_associated_orders`
     *
     * @return void
     */
    private function setupProductAssociatedOrders()
    {
        $getTable = $this->installer->getTable(Subscription::TBL_ASSOCIATE_PRODUCT_ORDER);
        
        $table = $this->connection->newTable($getTable);
        
        $table->addColumn(
            'relation_id',
            Table::TYPE_INTEGER,
            11,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Subscription relation unique id'
        )->addColumn(
            'subscription_id',
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false],
            'Product Subscription id'
        )->addColumn(
            'order_id',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'subscription order id'
        )->addForeignKey(
            Subscription::TBL_ASSOCIATE_PRODUCT_ORDER.'_subscription_id',
            'subscription_id',
            $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER),
            'subscription_id',
            Table::ACTION_CASCADE
        );
        
        $this->connection->createTable($table);
    }
    
    /**
     * Product Occurrence Table
     * create table `md_subscribenow_product_occurrence`
     *
     * @return void
     */
    private function setupProductOccurrence()
    {
        $getTable = $this->installer->getTable(Subscription::TBL_OCCURENCE_PRODUCT);
        
        $table = $this->connection->newTable($getTable);
        
        $table->addColumn(
            'occurrence_id',
            Table::TYPE_INTEGER,
            11,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Unique id for Occurrence'
        )->addColumn(
            'subscription_id',
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false],
            'Subscription Id'
        )->addIndex(
            $this->installer->getIdxName(
                Subscription::TBL_OCCURENCE_PRODUCT,
                ['subscription_id']
            ),
            ['subscription_id']
        )->addColumn(
            'occurrence_date',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Occurrence Date'
        )->addColumn(
            'order_id',
            Table::TYPE_TEXT,
            32,
            ['nullable' => false],
            'Subscription Order ID'
        )->addColumn(
            'order_status',
            Table::TYPE_TEXT,
            32,
            ['nullable' => false],
            'Subscription Order Status'
        )->addColumn(
            'occurrence_order',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Occurrence Order Status'
        )->addForeignKey(
            'md_subscribenow_product_subscription_subscription_id',
            'subscription_id',
            $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER),
            'subscription_id',
            Table::ACTION_CASCADE
        );
        
        $this->connection->createTable($table);
    }
    
    /**
     * Aggregated Product Table
     * create table `md_subscribenow_aggregated_product`
     *
     * @return void
     */
    private function setupAggregatedProduct()
    {
        $getTable = $this->installer->getTable(Subscription::TBL_AGGREGATE_PRODUCT);
        
        $table = $this->connection->newTable($getTable);
        
        $table->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'period',
            Table::TYPE_DATE,
            null,
            [],
            'Period'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Store Id'
        )->addColumn(
            'product_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Product Id'
        )->addColumn(
            'product_name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Product Name'
        )->addColumn(
            'subscriber_count',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Subscriber Count'
        )->addColumn(
            'active_subscriber',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Active Subscriber Count'
        )->addColumn(
            'pause_subscriber',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Pause Subscriber Count'
        )->addColumn(
            'cancel_subscriber',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Cancel Subscriber Count'
        )->addColumn(
            'no_of_occurrence',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'No of occurrence'
        )->addIndex(
            $this->installer->getIdxName(
                Subscription::TBL_AGGREGATE_PRODUCT,
                ['period', 'store_id', 'product_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['period', 'store_id', 'product_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $this->installer->getIdxName(
                Subscription::TBL_AGGREGATE_PRODUCT,
                ['store_id']
            ),
            ['store_id']
        )->addIndex(
            $this->installer->getIdxName(
                Subscription::TBL_AGGREGATE_PRODUCT,
                ['product_id']
            ),
            ['product_id']
        )->addForeignKey(
            $this->installer->getFkName(
                Subscription::TBL_AGGREGATE_PRODUCT,
                'store_id',
                'store',
                'store_id'
            ),
            'store_id',
            $this->installer->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $this->installer->getFkName(
                Subscription::TBL_AGGREGATE_PRODUCT,
                'product_id',
                'catalog_product_entity',
                'entity_id'
            ),
            'product_id',
            $this->installer->getTable('catalog_product_entity'),
            'entity_id',
            Table::ACTION_CASCADE
        )->setComment('Product Subscription Aggregated');
        
        $this->connection->createTable($table);
    }
    
    /**
     * Aggregated Customer Table
     * create table `md_subscribenow_aggregated_customer`
     *
     * @return void
     */
    private function setupAggregatedCustomer()
    {
        $getTable = $this->installer->getTable(Subscription::TBL_AGGREGATE_CUSTOMER);
        
        $table = $this->connection->newTable($getTable);
        
        $table->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'period',
            Table::TYPE_DATE,
            null,
            [],
            'Period'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Store Id'
        )->addColumn(
            'customer_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Customer Id'
        )->addColumn(
            'customer_name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Customer Name'
        )->addColumn(
            'customer_email',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Customer Email'
        )->addColumn(
            'subscriber_count',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Subscriber Count'
        )->addColumn(
            'active_subscriber',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Active Subscriber Count'
        )->addColumn(
            'pause_subscriber',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Pause Subscriber Count'
        )->addColumn(
            'cancel_subscriber',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Cancel Subscriber Count'
        )->addColumn(
            'no_of_occurrence',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'No of occurrence'
        )->addIndex(
            $this->installer->getIdxName(
                Subscription::TBL_AGGREGATE_CUSTOMER,
                ['period', 'store_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['period', 'store_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $this->installer->getIdxName(
                Subscription::TBL_AGGREGATE_CUSTOMER,
                ['store_id']
            ),
            ['store_id']
        )->addIndex(
            $this->installer->getIdxName(
                Subscription::TBL_AGGREGATE_CUSTOMER,
                ['customer_id']
            ),
            ['customer_id']
        )->addForeignKey(
            $this->installer->getFkName(
                Subscription::TBL_AGGREGATE_CUSTOMER,
                'store_id',
                'store',
                'store_id'
            ),
            'store_id',
            $this->installer->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $this->installer->getFkName(
                Subscription::TBL_AGGREGATE_CUSTOMER,
                'customer_id',
                'customer_entity',
                'entity_id'
            ),
            'customer_id',
            $this->installer->getTable('customer_entity'),
            'entity_id',
            Table::ACTION_CASCADE
        )->setComment('Customer Subscription Aggregated');
        
        $this->connection->createTable($table);
    }
    
    /**
     * Add Extension Custom Attribute for sales
     */
    private function addCustomExtensionAttribute()
    {
        $this->setupSalesOrderAttribute();
        $this->setupQuoteAttribute();
        $this->setupQuoteAddressAttribute();
        $this->setupSalesInvoiceAttribute();
        $this->setupSalesCreditMemoAttribute();
        $this->setupQuoteItemAttribute();
        $this->setupSalesOrderItemAttribute();
    }
    
    /**
     * Custom Attribute for Sales/Address/Quote/Invoice
     *
     * @param mixed $table
     * @return void
     */
    private function setCustomAttributeColumns($table)
    {
        if (!$table) {
            return;
        }
        
        $colums = [
            'subscribenow_init_amount' => [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,4',
                [],
                'comment' => 'Subscription Initial Amount'
            ],
            'base_subscribenow_init_amount' => [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,4',
                [],
                'comment' => 'Base Subscription Initial Amount'
            ],
            'subscribenow_trial_amount' => [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,4',
                [],
                'comment' => 'Subscription Trial Amount'
            ],
            'base_subscribenow_trial_amount' => [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,4',
                [],
                'comment' => 'Base Subscription Trial Amount'
            ]
        ];
        
        foreach ($colums as $column => $data) {
            $this->connection->addColumn($table, $column, $data);
        }
    }
    
    /**
     * Add Custom Attribute in `sales_order`
     *
     * @return void
     */
    private function setupSalesOrderAttribute()
    {
        $table = $this->installer->getTable('sales_order');
        $this->setCustomAttributeColumns($table);
        
        $this->connection->addColumn(
            $table,
            'has_trial',
            [
                'type' => Table::TYPE_SMALLINT,
                'length' => '6',
                ['unsigned' => true, 'nullable' => true],
                'comment' => 'Subscription Initial Amount'
            ]
        );
    }

    /**
     * Add Custom Attribute in `quote`
     *
     * @return void
     */
    private function setupQuoteAttribute()
    {
        $table = $this->installer->getTable('quote');
        
        $this->setCustomAttributeColumns($table);

        $this->installer->getConnection()->addColumn(
            $table,
            'md_cron_order',
            [
                'type' => Table::TYPE_SMALLINT,
                'length' => '6',
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'comment' => 'Subscription cron order flag',
            ]
        );

        $this->installer->getConnection()->addColumn(
            $table,
            'md_trial_set',
            [
                'type' => Table::TYPE_SMALLINT,
                'length' => '6',
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'comment' => 'Subscription trial order flag',
            ]
        );
    }
 
    /**
     * Add Custom Attribute in `quote_address`
     *
     * @return void
     */
    private function setupQuoteAddressAttribute()
    {
        $table = $this->installer->getTable('quote_address');
        $this->setCustomAttributeColumns($table);
    }
    
    /**
     * Add Custom Attribute in `sales_invoice`
     *
     * @return void
     */
    private function setupSalesInvoiceAttribute()
    {
        $table = $this->installer->getTable('sales_invoice');
        $this->setCustomAttributeColumns($table);
    }
    
    /**
     * Add Custom Attribute in `sales_creditmemo`
     *
     * @return void
     */
    private function setupSalesCreditMemoAttribute()
    {
        $table = $this->installer->getTable('sales_creditmemo');
        $this->setCustomAttributeColumns($table);
    }
    
    /**
     * Custom Attribute for Sales/Address/Quote/Invoice
     *
     * @param mixed $table
     * @return void
     */
    private function setCustomAttributeColumnsQuoteItem($table)
    {
        if (!$table) {
            return;
        }
        
        $columns = [
            'md_item_org_price' => [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,4',
                [],
                'comment' => 'Future date subscription price'
            ],
            'is_subscription' => [
                'type' => Table::TYPE_SMALLINT,
                'length' => '5',
                [],
                'comment' => 'Item subscription flag'
            ]
        ];
        
        foreach ($columns as $column => $data) {
            $this->connection->addColumn($table, $column, $data);
        }
    }
    
    /**
     * Add Custom Attribute in `quote_item`
     *
     * @return void
     */
    private function setupQuoteItemAttribute()
    {
        $table = $this->installer->getTable('quote_item');
        $this->setCustomAttributeColumnsQuoteItem($table);
    }
    
    /**
     * Add Custom Attribute in `sales_order_item`
     *
     * @return void
     */
    private function setupSalesOrderItemAttribute()
    {
        $table = $this->installer->getTable('sales_order_item');
        $this->setCustomAttributeColumnsQuoteItem($table);
    }
    
    /**
     * Add `initial_order` column md_subscribenow_product_subscribers
     * @return void
     */
    public function setupProductSubscriberAddCoulmn()
    {
        $table = $this->installer->getTable(Subscription::TBL_PRODUCT_SUBSCRIBER);
        
        $this->connection->addColumn(
            $table,
            'initial_order',
            [
                'type' => Table::TYPE_INTEGER,
                'length' => '10',
                [],
                'comment' => 'Subscription initial order number',
            ]
        );
    }
}
