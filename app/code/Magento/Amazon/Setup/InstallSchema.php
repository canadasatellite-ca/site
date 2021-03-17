<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Setup;

use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var SchemaSetupInterface $installer */
        $installer = $setup;

        /** @var AdapterInterface $connection */
        $connection = $installer->getConnection();

        $tables = [
            $this->createAccountTable($connection, $installer), // channel_amazon_account
            $this->createAccountListingTable($connection, $installer), // channel_amazon_account_listing
            $this->createAccountOrderTable($connection, $installer), // channel_amazon_account_order
            $this->createListingTable($connection, $installer), // channel_amazon_listing
            $this->createAccountDefectTable($connection, $installer), // channel_amazon_account_defect
            $this->createAccountListingLogTable($connection, $installer), // channel_amazon_listing_log
            $this->createAttributeTable($connection, $installer), // channel_amazon_attribute
            $this->createAttributeValueTable($connection, $installer), // channel_amazon_attribute_value
            $this->createPricingBestBuyBoxTable($connection, $installer), // channel_amazon_pricing_bestbuybox
            $this->createPricingLowestTable($connection, $installer), // channel_amazon_pricing_lowest
            $this->createListingRuleTable($connection, $installer), // channel_amazon_listing_rule
            $this->createPricingRuleTable($connection, $installer), // channel_amazon_pricing_rule
            $this->createListingMultipleTable($connection, $installer), // channel_amazon_listing_multiple
            $this->createListingVariantTable($connection, $installer), // channel_amazon_listing_variant
            $this->createActionTable($connection, $installer), // channel_amazon_action
            $this->createOrderTable($connection, $installer), // channel_amazon_order
            $this->createOrderItemTable($connection, $installer), // channel_amazon_order_item
            $this->createOrderReserveTable($connection, $installer), // channel_amazon_order_reserve
            $this->createOrderTrackingTable($connection, $installer), // channel_amazon_order_tracking
            $this->createErrorLogTable($connection, $installer), // channel_amazon_error_log
            $this->createQuantityIndexTable($connection, $installer), // channel_amazon_quantity_index
            $this->createPricingIndexTable($connection, $installer), // channel_amazon_pricing_index
            $this->createLogProcessingTable($connection, $installer), // channel_amazon_log_processing
        ];

        foreach ($tables as $table) {
            $connection->createTable($table);
        }
    }

    /**
     * @param AdapterInterface $connection
     * @param SchemaSetupInterface $installer
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createAccountTable(AdapterInterface $connection, SchemaSetupInterface $installer): Table
    {
        return $connection->newTable(
            $installer->getTable('channel_amazon_account')
        )->addColumn(
            'merchant_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'unsigned' => true, 'primary' => true],
            'Merchant Id'
        )->addColumn(
            'setup_step',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 1],
            'Setup Step'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => Definitions::ACCOUNT_STATUS_INCOMPLETE],
            'Is Active'
        )->addColumn(
            'seller_id',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Amazon Seller Id'
        )->addColumn(
            'country_code',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Service Id'
        )->addColumn(
            'base_url',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Base URL'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Account Name'
        )->addColumn(
            'email',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Email Address'
        )->addColumn(
            'base_url',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Base URL'
        )->addColumn(
            'consumer_key',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Consumer Key'
        )->addColumn(
            'consumer_secret',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Consumer Secret'
        )->addColumn(
            'access_token',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Access Token'
        )->addColumn(
            'access_secret',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Access Secret'
        )->addColumn(
            'report_run',
            Table::TYPE_BOOLEAN,
            null,
            ['nullable' => false, 'default' => 0],
            'All listing report run'
        )->addColumn(
            'created_on',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created On'
        )->addColumn(
            'uuid',
            Table::TYPE_TEXT,
            36,
            ['nullable' => false, 'comment' => 'UUID for merchant']
        )->addColumn(
            'last_updated',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Last Updated'
        )->addIndex(
            $installer->getIdxName('merchant_id', ['merchant_id']),
            ['merchant_id']
        )->addIndex(
            $installer->getIdxName(
                'channel_amazon_account',
                ['uniquevalue'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['country_code', 'seller_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        );
    }

    /**
     * @param AdapterInterface $connection
     * @param SchemaSetupInterface $installer
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createAccountListingTable(AdapterInterface $connection, SchemaSetupInterface $installer): Table
    {
        return $connection->newTable(
            $installer->getTable('channel_amazon_account_listing')
        )->addColumn(
            'id',
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn(
            'merchant_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'Merchant ID'
        )->addColumn(
            'auto_list',
            Table::TYPE_BOOLEAN,
            null,
            ['nullable' => false, 'default' => 1],
            'Automatically List Eligible Items'
        )->addColumn(
            'thirdparty_is_active',
            Table::TYPE_BOOLEAN,
            null,
            ['nullable' => false, 'default' => 0],
            'Third Party Import Is Active'
        )->addColumn(
            'thirdparty_sku_field',
            Table::TYPE_TEXT,
            200,
            ['nullable' => true, 'default' => null],
            'Third Party SKU Attribute Mapping Field'
        )->addColumn(
            'thirdparty_asin_field',
            Table::TYPE_TEXT,
            200,
            ['nullable' => true, 'default' => null],
            'Third Party ASIN Attribute Mapping Field'
        )->addColumn(
            'handling_time',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 2],
            'Amazon Default Handling Time'
        )->addColumn(
            'list_condition',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 11],
            'Default Amazon Product Condition'
        )->addColumn(
            'seller_notes',
            Table::TYPE_TEXT,
            2048,
            ['nullable' => true],
            'Default Seller Notes'
        )->addColumn(
            'list_condition_field',
            Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Default Amazon Product Condition Catalog Attribute'
        )->addColumn(
            'list_condition_new',
            Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Attribute Value Representing Condition New'
        )->addColumn(
            'list_condition_refurbished',
            Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Attribute Value Representing Condition Refurbished'
        )->addColumn(
            'seller_notes_refurbished',
            Table::TYPE_TEXT,
            2048,
            ['nullable' => true],
            'Default Seller Notes Refurbished'
        )->addColumn(
            'list_condition_likenew',
            Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Attribute Value Representing Condition Like New'
        )->addColumn(
            'seller_notes_likenew',
            Table::TYPE_TEXT,
            2048,
            ['nullable' => true],
            'Default Seller Notes Like New'
        )->addColumn(
            'list_condition_verygood',
            Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Attribute Value Representing Condition Very Good'
        )->addColumn(
            'seller_notes_verygood',
            Table::TYPE_TEXT,
            2048,
            ['nullable' => true],
            'Default Seller Notes Very Good'
        )->addColumn(
            'list_condition_good',
            Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Attribute Value Representing Condition Good'
        )->addColumn(
            'seller_notes_good',
            Table::TYPE_TEXT,
            2048,
            ['nullable' => true],
            'Default Seller Notes Good'
        )->addColumn(
            'list_condition_acceptable',
            Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Attribute Value Representing Condition Acceptable'
        )->addColumn(
            'seller_notes_acceptable',
            Table::TYPE_TEXT,
            2048,
            ['nullable' => true],
            'Default Seller Notes Acceptable'
        )->addColumn(
            'list_condition_collectible_likenew',
            Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Attribute Value Representing Condition Collectible Like New'
        )->addColumn(
            'seller_notes_collectible_likenew',
            Table::TYPE_TEXT,
            2048,
            ['nullable' => true],
            'Default Seller Notes Collectible Like New'
        )->addColumn(
            'list_condition_collectible_verygood',
            Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Attribute Value Representing Condition Collectible Very Good'
        )->addColumn(
            'seller_notes_collectible_verygood',
            Table::TYPE_TEXT,
            2048,
            ['nullable' => true],
            'Default Seller Notes Collectible Very Good'
        )->addColumn(
            'list_condition_collectible_good',
            Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Attribute Value Representing Condition Collectible Good'
        )->addColumn(
            'seller_notes_collectible_good',
            Table::TYPE_TEXT,
            2048,
            ['nullable' => true],
            'Default Seller Notes Collectible Good'
        )->addColumn(
            'list_condition_collectible_acceptable',
            Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Attribute Value Representing Condition Collectible Acceptable'
        )->addColumn(
            'seller_notes_collectible_acceptable',
            Table::TYPE_TEXT,
            2048,
            ['nullable' => true],
            'Default Seller Notes Collectible Acceptable'
        )->addColumn(
            'business_is_active',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Business Pricing Enabled'
        )->addColumn(
            'tier_is_active',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Business Tiered Pricing Enabled'
        )->addColumn(
            'qty_price_one',
            Table::TYPE_DECIMAL,
            null,
            ['nullable' => true],
            'Quantity Price Discount One'
        )->addColumn(
            'lower_bound_one',
            Table::TYPE_SMALLINT,
            [12, 2],
            ['nullable' => true],
            'Lower Bound Qty One'
        )->addColumn(
            'qty_price_two',
            Table::TYPE_DECIMAL,
            null,
            ['nullable' => true],
            'Quantity Price Discount Two'
        )->addColumn(
            'lower_bound_two',
            Table::TYPE_SMALLINT,
            [12, 2],
            ['nullable' => true],
            'Lower Bound Qty Two'
        )->addColumn(
            'qty_price_three',
            Table::TYPE_DECIMAL,
            null,
            ['nullable' => true],
            'Quantity Price Discount Three'
        )->addColumn(
            'lower_bound_three',
            Table::TYPE_SMALLINT,
            [12, 2],
            ['nullable' => true],
            'Lower Bound Qty Three'
        )->addColumn(
            'qty_price_four',
            Table::TYPE_DECIMAL,
            null,
            ['nullable' => true],
            'Quantity Price Discount Four'
        )->addColumn(
            'lower_bound_four',
            Table::TYPE_SMALLINT,
            [12, 2],
            ['nullable' => true],
            'Lower Bound Qty Four'
        )->addColumn(
            'qty_price_five',
            Table::TYPE_DECIMAL,
            null,
            ['nullable' => true],
            'Quantity Price Discount Five'
        )->addColumn(
            'lower_bound_five',
            Table::TYPE_SMALLINT,
            [12, 2],
            ['nullable' => true],
            'Lower Bound Qty Five'
        )->addColumn(
            'asin_mapping_field',
            Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Attribute Value Representing ASIN'
        )->addColumn(
            'ean_mapping_field',
            Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Attribute Value Representing EAN'
        )->addColumn(
            'gcid_mapping_field',
            Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Attribute Value Representing GCID'
        )->addColumn(
            'isbn_mapping_field',
            Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Attribute Value Representing ISBN'
        )->addColumn(
            'upc_mapping_field',
            Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Attribute Value Representing UPC'
        )->addColumn(
            'general_mapping_field',
            Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Attribute Value Representing General Search'
        )->addColumn(
            'fulfilled_by',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 1],
            'Amazon Fulfilled By Setting'
        )->addColumn(
            'fulfilled_by_field',
            Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Attribute Containing Amazon Fulfilled By'
        )->addColumn(
            'fulfilled_by_seller',
            Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Value Representing Fulfilled By Seller'
        )->addColumn(
            'fulfilled_by_amazon',
            Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Value Representing Fulfilled By Amazon'
        )->addColumn(
            'custom_qty',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => 100],
            'Custom Amazon Listing Qty'
        )->addColumn(
            'min_qty',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => 0],
            'Minimum Amazon Listing Qty'
        )->addColumn(
            'max_qty',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => 10000],
            'Maximum Amazon Listing Qty'
        )->addColumn(
            'price_field',
            Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Price Source For Amazon'
        )->addColumn(
            'strike_price_field',
            Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Strike Through Price Attribute'
        )->addColumn(
            'map_price_field',
            Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Minimum Advertised Price Attribute'
        )->addColumn(
            'vat_is_active',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'VAT Is Active Status'
        )->addColumn(
            'vat_percentage',
            Table::TYPE_DECIMAL,
            [12, 3],
            ['nullable' => false, 'default' => 0],
            'VAT Percentage Added To Price'
        )->addColumn(
            'cc_is_active',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Currency Conversion Enabled'
        )->addColumn(
            'cc_rate',
            Table::TYPE_TEXT,
            '50',
            ['nullable' => true],
            'Conversion Rate Source'
        )->addIndex(
            $installer->getIdxName('merchnat_id', ['merchant_id']),
            ['merchant_id']
        )->addIndex(
            $installer->getIdxName(
                'channel_amazon_account_listing',
                ['uniquevalue'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['merchant_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addForeignKey(
            $installer->getFkName(
                'channel_amazon_account_listing',
                'merchant_id',
                'channel_amazon_account',
                'merchant_id'
            ),
            'merchant_id',
            $installer->getTable('channel_amazon_account'),
            'merchant_id',
            Table::ACTION_CASCADE
        );
    }

    /**
     * @param AdapterInterface $connection
     * @param SchemaSetupInterface $installer
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createAccountOrderTable(AdapterInterface $connection, SchemaSetupInterface $installer): Table
    {
        return $connection->newTable(
            $installer->getTable('channel_amazon_account_order')
        )->addColumn(
            'id',
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn(
            'merchant_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'Merchant ID'
        )->addColumn(
            'per_shipment',
            Table::TYPE_DECIMAL,
            [12, 2],
            ['nullable' => true],
            'Per Shipment Rate'
        )->addColumn(
            'order_is_active',
            Table::TYPE_BOOLEAN,
            null,
            ['nullable' => false, 'default' => 0],
            'Order Import Flag'
        )->addColumn(
            'default_store',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Store Association For Order Import'
        )->addColumn(
            'customer_is_active',
            Table::TYPE_BOOLEAN,
            null,
            ['nullable' => false, 'default' => 0],
            'Create Customer Is Active'
        )->addColumn(
            'is_external_order_id',
            Table::TYPE_BOOLEAN,
            null,
            ['nullable' => false, 'default' => 0],
            'Use External Order ID'
        )->addColumn(
            'reserve',
            Table::TYPE_BOOLEAN,
            null,
            ['nullable' => false, 'default' => 0],
            'Reserve Inventory Flag'
        )->addColumn(
            'custom_status_is_active',
            Table::TYPE_BOOLEAN,
            null,
            ['nullable' => false, 'default' => 0],
            'Order Status'
        )->addColumn(
            'custom_status',
            Table::TYPE_TEXT,
            80,
            ['nullable' => true],
            'Custom Processing Order State'
        )->addIndex(
            $installer->getIdxName('merchnat_id', ['merchant_id']),
            ['merchant_id']
        )->addIndex(
            $installer->getIdxName(
                'channel_amazon_account_order',
                ['uniquevalue'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['merchant_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addForeignKey(
            $installer->getFkName(
                'channel_amazon_account_order',
                'merchant_id',
                'channel_amazon_account',
                'merchant_id'
            ),
            'merchant_id',
            $installer->getTable('channel_amazon_account'),
            'merchant_id',
            Table::ACTION_CASCADE
        );
    }

    /**
     * @param AdapterInterface $connection
     * @param SchemaSetupInterface $installer
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createListingTable(AdapterInterface $connection, SchemaSetupInterface $installer): Table
    {
        return $connection->newTable(
            $installer->getTable('channel_amazon_listing')
        )->addColumn(
            'id',
            Table::TYPE_BIGINT,
            null,
            ['identity' => true, 'nullable' => false, 'unsigned' => true, 'primary' => true],
            'Id'
        )->addColumn(
            'merchant_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Merchant Id'
        )->addColumn(
            'listing_id',
            Table::TYPE_TEXT,
            50,
            ['nullable' => true],
            'Listing Id'
        )->addColumn(
            'catalog_product_id',
            Table::TYPE_TEXT,
            50,
            ['nullable' => true],
            'Catalog Product Id'
        )->addColumn(
            'product_id_type',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Amazon Product Id Type (ASIN, UPC, etc)'
        )->addColumn(
            'product_id',
            Table::TYPE_TEXT,
            50,
            ['nullable' => true, 'default' => null],
            'Amazon Product Id'
        )->addColumn(
            'product_type',
            Table::TYPE_TEXT,
            50,
            ['nullable' => true, 'default' => null],
            'Amazon Reported Product Type'
        )->addColumn(
            'category_id',
            Table::TYPE_TEXT,
            50,
            ['nullable' => true, 'default' => null],
            'Category Id'
        )->addColumn(
            'asin',
            Table::TYPE_TEXT,
            20,
            ['nullable' => true],
            'ASIN'
        )->addColumn(
            'catalog_sku',
            Table::TYPE_TEXT,
            100,
            ['nullable' => true],
            'Catalog SKU'
        )->addColumn(
            'seller_sku',
            Table::TYPE_TEXT,
            40,
            ['nullable' => true],
            'Amazon Seller SKU'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            500,
            ['nullable' => true],
            'Amazon Name'
        )->addColumn(
            'qty',
            Table::TYPE_DECIMAL,
            '12',
            ['nullable' => false, 'default' => 0],
            'Amazon Listing Qty'
        )->addColumn(
            'list_price',
            Table::TYPE_DECIMAL,
            [12, 2],
            ['nullable' => false, 'default' => 0.00],
            'Amazon Listing Price'
        )->addColumn(
            'shipping_price',
            Table::TYPE_DECIMAL,
            [12, 2],
            ['nullable' => false, 'default' => 0.00],
            'Amazon Shiping Price'
        )->addColumn(
            'landed_price',
            Table::TYPE_DECIMAL,
            [12, 2],
            ['nullable' => false, 'default' => 0.00],
            'Amazon Landed Price'
        )->addColumn(
            'msrp_price',
            Table::TYPE_DECIMAL,
            [12, 2],
            ['nullable' => false, 'default' => 0.00],
            'Amazon MSRP Price'
        )->addColumn(
            'map_price',
            Table::TYPE_DECIMAL,
            [12, 2],
            ['nullable' => false, 'default' => 0.00],
            'Amazon MAP Price'
        )->addColumn(
            'variants',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Variants Exist'
        )->addColumn(
            'condition',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Listing Condition'
        )->addColumn(
            'is_listed',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Is Live On Amazon'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Is Active On Amazon'
        )->addColumn(
            'eligible',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 1],
            'Is Eligible For Listing'
        )->addColumn(
            'list_status',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 98],
            'Listing Status'
        )->addColumn(
            'fulfilled_by',
            Table::TYPE_TEXT,
            50,
            ['nullable' => true],
            'Fulfilled By Flag'
        )->addColumn(
            'created_on',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created On'
        )->addColumn(
            'updated_on',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Updated On'
        )->addColumn(
            'pricing_update',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Pricing Update Flag (used for index process)'
        )->addColumn(
            'quantity_update',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Quantity Update Flag (used for index process)'
        )->addColumn(
            'fulfilled_by_update',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 1],
            'Fulfilled By Update Flag'
        )->addColumn(
            'is_ship',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Shiping Charges Calculated Flag'
        )->addColumn(
            'condition_override',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Listing Condition Override Value'
        )->addColumn(
            'condition_notes_override',
            Table::TYPE_TEXT,
            2048,
            ['nullable' => true, 'default' => null],
            'Listing Condition Notes Override Value'
        )->addColumn(
            'list_price_override',
            Table::TYPE_DECIMAL,
            [12, 2],
            ['nullable' => true, 'default' => null],
            'Amazon Listing Price Override'
        )->addColumn(
            'handling_override',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => true, 'default' => null],
            'Handling Time Override'
        )->addIndex(
            $installer->getIdxName('merchant_id', ['merchant_id']),
            ['merchant_id']
        )->addIndex(
            $installer->getIdxName('listing_id', ['listing_id']),
            ['listing_id']
        )->addIndex(
            $installer->getIdxName('catalog_product_id', ['catalog_product_id']),
            ['catalog_product_id']
        )->addIndex(
            $installer->getIdxName('asin', ['asin']),
            ['asin']
        )->addIndex(
            $installer->getIdxName('condition', ['condition']),
            ['condition']
        )->addIndex(
            $installer->getIdxName('list_status', ['list_status']),
            ['list_status']
        )->addIndex(
            $installer->getIdxName('is_ship', ['is_ship']),
            ['is_ship']
        )->addIndex(
            $installer->getIdxName(
                'channel_amazon_account_listing',
                ['uniquevalue'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['merchant_id', 'seller_sku'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addForeignKey(
            $installer->getFkName(
                'channel_amazon_listing',
                'merchant_id',
                'channel_amazon_account',
                'merchant_id'
            ),
            'merchant_id',
            $installer->getTable('channel_amazon_account'),
            'merchant_id',
            Table::ACTION_CASCADE
        );
    }

    /**
     * @param AdapterInterface $connection
     * @param SchemaSetupInterface $installer
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createAccountDefectTable(AdapterInterface $connection, SchemaSetupInterface $installer): Table
    {
        return $connection->newTable(
            $installer->getTable('channel_amazon_defect')
        )->addColumn(
            'id',
            Table::TYPE_BIGINT,
            null,
            ['identity' => true, 'nullable' => false, 'unsigned' => true, 'primary' => true],
            'Id'
        )->addColumn(
            'merchant_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Merchant Id'
        )->addColumn(
            'seller_sku',
            Table::TYPE_TEXT,
            250,
            ['nullable' => true],
            'Seller SKU'
        )->addColumn(
            'asin',
            Table::TYPE_TEXT,
            30,
            ['nullable' => true],
            'ASIN'
        )->addColumn(
            'field_name',
            Table::TYPE_TEXT,
            50,
            ['nullable' => true],
            'Amazon Defect Name'
        )->addColumn(
            'alert_type',
            Table::TYPE_TEXT,
            50,
            ['nullable' => true],
            'Amazon Alert Type'
        )->addColumn(
            'notes',
            Table::TYPE_TEXT,
            300,
            ['nullable' => true],
            'Notes'
        )->addIndex(
            $installer->getIdxName(
                'channel_amazon_account_defect',
                ['uniquevalue'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['merchant_id', 'seller_sku', 'alert_type'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addForeignKey(
            $installer->getFkName(
                'channel_amazon_defect',
                'merchant_id',
                'channel_amazon_account',
                'merchant_id'
            ),
            'merchant_id',
            $installer->getTable('channel_amazon_account'),
            'merchant_id',
            Table::ACTION_CASCADE
        );
    }

    /**
     * @param AdapterInterface $connection
     * @param SchemaSetupInterface $installer
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createAccountListingLogTable(AdapterInterface $connection, SchemaSetupInterface $installer): Table
    {
        return $connection->newTable(
            $installer->getTable('channel_amazon_listing_log')
        )->addColumn(
            'id',
            Table::TYPE_BIGINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'merchant_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Merchant Id'
        )->addColumn(
            'seller_sku',
            Table::TYPE_TEXT,
            40,
            ['nullable' => true],
            'Amazon Seller SKU'
        )->addColumn(
            'action',
            Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Api Action'
        )->addColumn(
            'status',
            Table::TYPE_TEXT,
            128,
            ['nullable' => true],
            'Api Status'
        )->addColumn(
            'notes',
            Table::TYPE_TEXT,
            2048,
            ['nullable' => true],
            'Notes'
        )->addColumn(
            'created_on',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created On'
        )->addIndex(
            $installer->getIdxName('id', ['id']),
            ['id']
        )->addForeignKey(
            $installer->getFkName(
                'channel_amazon_listing_log',
                'merchant_id',
                'channel_amazon_account',
                'merchant_id'
            ),
            'merchant_id',
            $installer->getTable('channel_amazon_account'),
            'merchant_id',
            Table::ACTION_CASCADE
        );
    }

    /**
     * @param AdapterInterface $connection
     * @param SchemaSetupInterface $installer
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createAttributeTable(AdapterInterface $connection, SchemaSetupInterface $installer): Table
    {
        return $connection->newTable(
            $installer->getTable('channel_amazon_attribute')
        )->addColumn(
            'id',
            Table::TYPE_BIGINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'amazon_attribute',
            Table::TYPE_TEXT,
            100,
            ['nullable' => true],
            'Amazon Attribute Name'
        )->addColumn(
            'catalog_attribute',
            Table::TYPE_TEXT,
            100,
            ['nullable' => true, 'default' => null],
            'Catalog Attribute Name'
        )->addColumn(
            'country_code',
            Table::TYPE_TEXT,
            50,
            ['nullable' => true],
            'Country Code'
        )->addColumn(
            'overwrite',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Overwrite Existing Attribute Value'
        )->addColumn(
            'type',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Text vs Select Type'
        )->addColumn(
            'store_ids',
            Table::TYPE_TEXT,
            512,
            ['nullable' => true, 'default' => null],
            'Store Ids'
        )->addColumn(
            'attribute_set_ids',
            Table::TYPE_TEXT,
            512,
            ['nullable' => true, 'default' => null],
            'Attribute Set Ids'
        )->addColumn(
            'in_search',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Use In Search'
        )->addColumn(
            'comparable',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Comparable'
        )->addColumn(
            'in_navigation',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Used in Layered Navigation'
        )->addColumn(
            'in_search_navigation',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Used in Search Rsults Layered Navigation'
        )->addColumn(
            'position',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Position (execution priority)'
        )->addColumn(
            'in_promo',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Use For Promo'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Is Active'
        )->addIndex(
            $installer->getIdxName('id', ['id']),
            ['id']
        )->addIndex(
            $installer->getIdxName('amazon_attribute', ['amazon_attribute']),
            ['amazon_attribute']
        )->addIndex(
            $installer->getIdxName(
                'channel_amazon_attribute',
                ['uniquevalue'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['amazon_attribute', 'country_code'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        );
    }

    /**
     * @param AdapterInterface $connection
     * @param SchemaSetupInterface $installer
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createAttributeValueTable(AdapterInterface $connection, SchemaSetupInterface $installer): Table
    {
        return $connection->newTable(
            $installer->getTable('channel_amazon_attribute_value')
        )->addColumn(
            'id',
            Table::TYPE_BIGINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'parent_id',
            Table::TYPE_BIGINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Parent Id'
        )->addColumn(
            'amazon_attribute',
            Table::TYPE_TEXT,
            250,
            ['nullable' => true],
            'Amazon attribute name'
        )->addColumn(
            'country_code',
            Table::TYPE_TEXT,
            50,
            ['nullable' => true],
            'Country Code'
        )->addColumn(
            'sku',
            Table::TYPE_TEXT,
            250,
            ['nullable' => true],
            'SKU'
        )->addColumn(
            'asin',
            Table::TYPE_TEXT,
            50,
            ['nullable' => true],
            'Asin'
        )->addColumn(
            'value',
            Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Attribute Value'
        )->addColumn(
            'status',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Import Status'
        )->addIndex(
            $installer->getIdxName('id', ['id']),
            ['id']
        )->addIndex(
            $installer->getIdxName('parent_id', ['parent_id']),
            ['parent_id']
        )->addIndex(
            $installer->getIdxName('asin', ['asin']),
            ['asin']
        )->addIndex(
            $installer->getIdxName(
                'channel_amazon_attribute_value',
                ['uniquevalue'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['parent_id', 'sku'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addForeignKey(
            $installer->getFkName(
                'channel_amazon_attribute_value',
                'parent_id',
                'channel_amazon_attribute',
                'id'
            ),
            'parent_id',
            $installer->getTable('channel_amazon_attribute'),
            'id',
            Table::ACTION_CASCADE
        );
    }

    /**
     * @param AdapterInterface $connection
     * @param SchemaSetupInterface $installer
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createPricingBestBuyBoxTable(AdapterInterface $connection, SchemaSetupInterface $installer): Table
    {
        $table = $connection->newTable(
            $installer->getTable('channel_amazon_pricing_bestbuybox')
        )->addColumn(
            'id',
            Table::TYPE_BIGINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'asin',
            Table::TYPE_TEXT,
            30,
            ['nullable' => true],
            'ASIN'
        )->addColumn(
            'is_seller',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Is Seller'
        )->addColumn(
            'country_code',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Country Code'
        )->addColumn(
            'condition_code',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Amazon Condition Code'
        )->addColumn(
            'condition',
            Table::TYPE_TEXT,
            30,
            ['nullable' => true],
            'Amazon Condition'
        )->addColumn(
            'subcondition',
            Table::TYPE_TEXT,
            30,
            ['nullable' => true],
            'Amazon Sub Condition'
        )->addColumn(
            'currency_code',
            Table::TYPE_TEXT,
            10,
            ['nullable' => true],
            'Currency Code Used'
        )->addIndex(
            $installer->getIdxName(
                'channel_amazon_pricing_bestbuybox',
                ['uniquevalue'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['asin', 'country_code', 'condition_code'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        );
        // add price columns
        $table = $this->buildPriceColumns($table, $installer);
        return $table;
    }

    /**
     * Builds price columns used in more than one table
     *
     * @param Table $table
     * @param SchemaSetupInterface $installer
     *
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function buildPriceColumns(Table $table, SchemaSetupInterface $installer): Table
    {
        return $table->addColumn(
            'landed_price',
            Table::TYPE_DECIMAL,
            [12, 2],
            ['nullable' => false, 'default' => 0.0000],
            'Amazon Landed Price'
        )->addColumn(
            'list_price',
            Table::TYPE_DECIMAL,
            [12, 2],
            ['nullable' => false, 'default' => 0.0000],
            'Amazon Listing Price'
        )->addColumn(
            'shipping_price',
            Table::TYPE_DECIMAL,
            [12, 2],
            ['nullable' => false, 'default' => 0.0000],
            'Amazon Shipping Price'
        )->addColumn(
            'last_updated',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Last Updated'
        )->addIndex(
            $installer->getIdxName('asin', ['asin']),
            ['asin']
        )->addIndex(
            $installer->getIdxName('country_code', ['country_code']),
            ['country_code']
        );
    }

    /**
     * @param AdapterInterface $connection
     * @param SchemaSetupInterface $installer
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createPricingLowestTable(AdapterInterface $connection, SchemaSetupInterface $installer): Table
    {
        $table = $connection->newTable(
            $installer->getTable('channel_amazon_pricing_lowest')
        )->addColumn(
            'id',
            Table::TYPE_BIGINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'asin',
            Table::TYPE_TEXT,
            30,
            ['nullable' => true],
            'ASIN'
        )->addColumn(
            'country_code',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Country Code'
        )->addColumn(
            'condition_code',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Amazon Condition Code'
        )->addColumn(
            'condition',
            Table::TYPE_TEXT,
            30,
            ['nullable' => true],
            'Amazon Condition'
        )->addColumn(
            'subcondition',
            Table::TYPE_TEXT,
            30,
            ['nullable' => true],
            'Amazon Sub Condition'
        )->addColumn(
            'fulfillment_channel',
            Table::TYPE_TEXT,
            30,
            ['nullable' => true],
            'Fulfillment Channel'
        )->addColumn(
            'feedback_rating',
            Table::TYPE_TEXT,
            30,
            ['nullable' => true],
            'Feedback Rating'
        )->addColumn(
            'feedback_count',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Feedback Count'
        )->addColumn(
            'currency_code',
            Table::TYPE_TEXT,
            10,
            ['nullable' => true],
            'Default Currency Code'
        );

        // add price columns
        $table = $this->buildPriceColumns($table, $installer);
        return $table;
    }

    /**
     * @param AdapterInterface $connection
     * @param SchemaSetupInterface $installer
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createListingRuleTable(AdapterInterface $connection, SchemaSetupInterface $installer): Table
    {
        return $connection->newTable(
            $installer->getTable('channel_amazon_listing_rule')
        )->addColumn(
            'id',
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Listing Rule Id'
        )->addColumn(
            'merchant_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Merchant Id'
        )->addColumn(
            'conditions_serialized',
            Table::TYPE_TEXT,
            2048,
            ['nullable' => true],
            'Conditions Serialized'
        )->addColumn(
            'website_id',
            Table::TYPE_SMALLINT,
            256,
            ['nullable' => false, 'unsigned' => true],
            'Website Id'
        )->addIndex(
            $installer->getIdxName(
                'channel_amazon_listing_rule',
                ['merchant_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['merchant_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $installer->getIdxName('merchant_id', ['merchant_id']),
            ['merchant_id']
        )->addForeignKey(
            $installer->getFkName(
                'channel_amazon_listing_rule',
                'merchant_id',
                'channel_amazon_account',
                'merchant_id'
            ),
            'merchant_id',
            $installer->getTable('channel_amazon_account'),
            'merchant_id',
            Table::ACTION_CASCADE
        );
    }

    /**
     * @param AdapterInterface $connection
     * @param SchemaSetupInterface $installer
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createPricingRuleTable(AdapterInterface $connection, SchemaSetupInterface $installer): Table
    {
        return $connection->newTable(
            $installer->getTable('channel_amazon_pricing_rule')
        )->addColumn(
            'id',
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'merchant_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Merchant Id'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            [],
            'Rule Name'
        )
            ->addColumn(
                'description',
                Table::TYPE_TEXT,
                '64k',
                [],
                'Rule Description'
            )
            ->addColumn(
                'from_date',
                Table::TYPE_TIMESTAMP,
                null,
                [],
                'From'
            )
            ->addColumn(
                'to_date',
                Table::TYPE_TIMESTAMP,
                null,
                [],
                'To'
            )
            ->addColumn(
                'is_active',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '0'],
                'Is Active'
            )
            ->addColumn(
                'conditions_serialized',
                Table::TYPE_TEXT,
                '2M',
                [],
                'Conditions Serialized'
            )
            ->addColumn(
                'stop_rules_processing',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '1'],
                'Stop Rules Processing'
            )
            ->addColumn(
                'sort_order',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Sort Order'
            )
            ->addColumn(
                'price_movement',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Price Movement (Increase, Decrease, Match)'
            )
            ->addColumn(
                'simple_action',
                Table::TYPE_TEXT,
                32,
                [],
                'By Fixed or Percentage'
            )
            ->addColumn(
                'discount_amount',
                Table::TYPE_DECIMAL,
                [12, 2],
                ['nullable' => false, 'default' => 0.0000],
                'Applied Discount Amount'
            )
            ->addColumn(
                'auto',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Manual - Auto'
            )
            ->addColumn(
                'auto_source',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Amazon Price Source'
            )
            ->addColumn(
                'auto_minimum_feedback',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Minimum Feedback Rating'
            )
            ->addColumn(
                'auto_feedback_count',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Minimum Feedback Count'
            )
            ->addColumn(
                'auto_condition',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'If Condition Variance'
            )
            ->addColumn(
                'new_variance',
                Table::TYPE_DECIMAL,
                [12, 2],
                ['nullable' => false, 'default' => 100.0000],
                'New Condition Variance'
            )
            ->addColumn(
                'refurbished_variance',
                Table::TYPE_DECIMAL,
                [12, 2],
                ['nullable' => false, 'default' => 100.0000],
                'Refuribshed Condition Variance'
            )
            ->addColumn(
                'usedlikenew_variance',
                Table::TYPE_DECIMAL,
                [12, 2],
                ['nullable' => false, 'default' => 100.0000],
                'Used Like New Condition Variance'
            )
            ->addColumn(
                'usedverygood_variance',
                Table::TYPE_DECIMAL,
                [12, 2],
                ['nullable' => false, 'default' => 100.0000],
                'Used Very Good Condition Variance'
            )
            ->addColumn(
                'usedgood_variance',
                Table::TYPE_DECIMAL,
                [12, 2],
                ['nullable' => false, 'default' => 100.0000],
                'Used Good Condition Variance'
            )
            ->addColumn(
                'usedacceptable_variance',
                Table::TYPE_DECIMAL,
                [12, 2],
                ['nullable' => false, 'default' => 100.0000],
                'Used Acceptable Condition Variance'
            )
            ->addColumn(
                'collectiblelikenew_variance',
                Table::TYPE_DECIMAL,
                [12, 2],
                ['nullable' => false, 'default' => 100.0000],
                'Collectible Like New Condition Variance'
            )
            ->addColumn(
                'collectibleverygood_variance',
                Table::TYPE_DECIMAL,
                [12, 2],
                ['nullable' => false, 'default' => 100.0000],
                'Collectible Very Good Condition Variance'
            )
            ->addColumn(
                'collectiblegood_variance',
                Table::TYPE_DECIMAL,
                [12, 2],
                ['nullable' => false, 'default' => 100.0000],
                'Collectible Good Condition Variance'
            )
            ->addColumn(
                'collectibleacceptable_variance',
                Table::TYPE_DECIMAL,
                [12, 2],
                ['nullable' => false, 'default' => 100.0000],
                'Collectible Acceptable Condition Variance'
            )
            ->addColumn(
                'floor',
                Table::TYPE_TEXT,
                32,
                [],
                'Attribute For Floor Limit'
            )
            ->addColumn(
                'floor_price_movement',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Floor Movement (Increase, Decrease, Match)'
            )
            ->addColumn(
                'floor_simple_action',
                Table::TYPE_TEXT,
                32,
                [],
                'Fixed vs Percentage Discount'
            )
            ->addColumn(
                'floor_discount_amount',
                Table::TYPE_DECIMAL,
                [12, 2],
                ['nullable' => false, 'default' => 0.0000],
                'Floor Discount Amount'
            )
            ->addColumn(
                'ceiling',
                Table::TYPE_TEXT,
                32,
                [],
                'Attribute For Ceiling Limit'
            )
            ->addColumn(
                'ceiling_price_movement',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Ceiling Movement (Increase, Decrease, Match)'
            )
            ->addColumn(
                'ceiling_simple_action',
                Table::TYPE_TEXT,
                32,
                [],
                'Fixed vs Percentage Discount'
            )
            ->addColumn(
                'ceiling_discount_amount',
                Table::TYPE_DECIMAL,
                [12, 2],
                ['nullable' => false, 'default' => 0.0000],
                'Ceiling Discount Amount'
            )
            ->addIndex(
                $installer->getIdxName('id', ['id']),
                ['id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'channel_amazon_pricing_rule',
                    'merchant_id',
                    'channel_amazon_account',
                    'merchant_id'
                ),
                'merchant_id',
                $installer->getTable('channel_amazon_account'),
                'merchant_id',
                Table::ACTION_CASCADE
            );
    }

    /**
     * @param AdapterInterface $connection
     * @param SchemaSetupInterface $installer
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createListingMultipleTable(AdapterInterface $connection, SchemaSetupInterface $installer): Table
    {
        return $connection->newTable(
            $installer->getTable('channel_amazon_listing_multiple')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'parent_id',
            Table::TYPE_BIGINT,
            null,
            ['nullable' => false, 'unsigned' => true],
            'Parent ID'
        )->addColumn(
            'asin',
            Table::TYPE_TEXT,
            30,
            ['nullable' => true],
            'ASIN'
        )->addColumn(
            'title',
            Table::TYPE_TEXT,
            512,
            ['nullable' => true],
            'Title'
        )->addColumn(
            'product_type',
            Table::TYPE_TEXT,
            100,
            ['nullable' => true],
            'Amazon Product Type'
        )->addIndex(
            $installer->getIdxName(
                'channel_amazon_listing_multiple',
                ['uniquevalue'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['parent_id', 'asin'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addForeignKey(
            $installer->getFkName(
                'channel_amazon_listing_multiple',
                'parent_id',
                'channel_amazon_listing',
                'id'
            ),
            'parent_id',
            $installer->getTable('channel_amazon_listing'),
            'id',
            Table::ACTION_CASCADE
        );
    }

    /**
     * @param AdapterInterface $connection
     * @param SchemaSetupInterface $installer
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createListingVariantTable(AdapterInterface $connection, SchemaSetupInterface $installer): Table
    {
        return $connection->newTable(
            $installer->getTable('channel_amazon_listing_variant')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'unsigned' => true, 'primary' => true],
            'Id'
        )->addColumn(
            'parent_id',
            Table::TYPE_BIGINT,
            null,
            ['nullable' => false, 'unsigned' => true],
            'Parent ID'
        )->addColumn(
            'asin',
            Table::TYPE_TEXT,
            30,
            ['nullable' => true],
            'ASIN'
        )->addColumn(
            'variant_name',
            Table::TYPE_TEXT,
            512,
            ['nullable' => true],
            'Amazon Variant Name'
        )->addColumn(
            'variant_value',
            Table::TYPE_TEXT,
            512,
            ['nullable' => true],
            'Amazon Variant Value'
        )->addIndex(
            $installer->getIdxName(
                'channel_amazon_listing_variant',
                ['uniquevalue'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['parent_id', 'asin'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addForeignKey(
            $installer->getFkName(
                'channel_amazon_listing_variant',
                'parent_id',
                'channel_amazon_listing',
                'id'
            ),
            'parent_id',
            $installer->getTable('channel_amazon_listing'),
            'id',
            Table::ACTION_CASCADE
        );
    }

    /**
     * @param AdapterInterface $connection
     * @param SchemaSetupInterface $installer
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createActionTable(AdapterInterface $connection, SchemaSetupInterface $installer): Table
    {
        return $connection->newTable(
            $installer->getTable('channel_amazon_action')
        )->addColumn(
            'id',
            Table::TYPE_BIGINT,
            null,
            ['identity' => true, 'nullable' => false, 'unsigned' => true, 'primary' => true],
            'Action Id'
        )->addColumn(
            'merchant_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'Merchant Id'
        )->addColumn(
            'identifier',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Unique Identifier'
        )->addColumn(
            'command',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Command'
        )->addColumn(
            'command_body',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Command Body'
        )->addForeignKey(
            $installer->getFkName(
                'channel_amazon_action',
                'merchant_id',
                'channel_amazon_account',
                'merchant_id'
            ),
            'merchant_id',
            $installer->getTable('channel_amazon_account'),
            'merchant_id',
            Table::ACTION_CASCADE
        )->addIndex(
            $installer->getIdxName(
                'channel_amazon_action',
                ['uniquevalue_command'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['merchant_id', 'identifier', 'command'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        );
    }

    /**
     * @param AdapterInterface $connection
     * @param SchemaSetupInterface $installer
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createOrderTable(AdapterInterface $connection, SchemaSetupInterface $installer): Table
    {
        return $connection->newTable(
            $installer->getTable('channel_amazon_order')
        )->addColumn(
            'id',
            Table::TYPE_BIGINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'merchant_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'Merchant Id'
        )->addColumn(
            'order_id',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Amazon Order Id'
        )->addColumn(
            'sales_order_id',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Magento Order Id'
        )->addColumn(
            'sales_order_number',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Magento Order Number'
        )->addColumn(
            'status',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false, 'default' => 'Pending'],
            'Order Status'
        )->addColumn(
            'buyer_email',
            Table::TYPE_TEXT,
            250,
            ['nullable' => true, 'default' => null],
            'Buyer Email'
        )->addColumn(
            'ship_service_level',
            Table::TYPE_TEXT,
            250,
            ['nullable' => true, 'default' => null],
            'Ship Service Level'
        )->addColumn(
            'sales_channel',
            Table::TYPE_TEXT,
            50,
            ['nullable' => true, 'default' => null],
            'Sales Channel'
        )->addColumn(
            'shipped_by_amazon',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Shipped By Amazon'
        )->addColumn(
            'is_business',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Is Business Address Flag'
        )->addColumn(
            'items_shipped',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Items Shipped Count'
        )->addColumn(
            'items_unshipped',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Items Unshipped Count'
        )->addColumn(
            'buyer_name',
            Table::TYPE_TEXT,
            50,
            ['nullable' => true, 'default' => null],
            'Buyer Name'
        )->addColumn(
            'currency',
            Table::TYPE_TEXT,
            10,
            ['nullable' => true, 'default' => null],
            'Order Currency'
        )->addColumn(
            'total',
            Table::TYPE_DECIMAL,
            [12, 2],
            ['nullable' => false, 'default' => 0.0000],
            'Order Grand Total'
        )->addColumn(
            'is_premium',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Is Premium Flag'
        )->addColumn(
            'is_prime',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Is Prime Flag'
        )->addColumn(
            'purchase_order_number',
            Table::TYPE_TEXT,
            300,
            ['nullable' => true, 'default' => null],
            'Purchase Order Number'
        )->addColumn(
            'is_replacement',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Is Replacement Order'
        )->addColumn(
            'fulfillment_channel',
            Table::TYPE_TEXT,
            10,
            ['nullable' => true, 'default' => null],
            'Fulfillment Channel'
        )->addColumn(
            'payment_method',
            Table::TYPE_TEXT,
            25,
            ['nullable' => true, 'default' => null],
            'Payment Method'
        )->addColumn(
            'service_level',
            Table::TYPE_TEXT,
            60,
            ['nullable' => true, 'default' => null],
            'Service Level'
        )->addColumn(
            'ship_name',
            Table::TYPE_TEXT,
            200,
            ['nullable' => true, 'default' => null],
            'Ship Name'
        )->addColumn(
            'ship_address_one',
            Table::TYPE_TEXT,
            300,
            ['nullable' => true, 'default' => null],
            'Ship Address Line One'
        )->addColumn(
            'ship_address_two',
            Table::TYPE_TEXT,
            300,
            ['nullable' => true, 'default' => null],
            'Ship Address Line Two'
        )->addColumn(
            'ship_address_three',
            Table::TYPE_TEXT,
            300,
            ['nullable' => true, 'default' => null],
            'Ship Address Line Three'
        )->addColumn(
            'ship_city',
            Table::TYPE_TEXT,
            200,
            ['nullable' => true, 'default' => null],
            'Ship City'
        )->addColumn(
            'ship_region',
            Table::TYPE_TEXT,
            60,
            ['nullable' => true, 'default' => null],
            'Ship Region'
        )->addColumn(
            'ship_postal_code',
            Table::TYPE_TEXT,
            20,
            ['nullable' => true, 'default' => null],
            'Ship Postal Code'
        )->addColumn(
            'ship_country',
            Table::TYPE_TEXT,
            20,
            ['nullable' => true, 'default' => null],
            'Ship Country'
        )->addColumn(
            'ship_phone',
            Table::TYPE_TEXT,
            60,
            ['nullable' => true, 'default' => null],
            'Ship Phone'
        )->addColumn(
            'purchase_date',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Purchase Date'
        )->addColumn(
            'latest_ship_date',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true, 'default' => null],
            'Latest Ship Date'
        )->addColumn(
            'reserved',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Reserved Quantity'
        )->addColumn(
            'notes',
            Table::TYPE_TEXT,
            500,
            ['nullable' => true, 'default' => null],
            'Notes'
        )->addIndex(
            $installer->getIdxName('order_id', ['order_id']),
            ['order_id']
        )->addIndex(
            $installer->getIdxName(
                'channel_amazon_order',
                ['uniquevalue'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['order_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        );
    }

    /**
     * @param AdapterInterface $connection
     * @param SchemaSetupInterface $installer
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createOrderItemTable(AdapterInterface $connection, SchemaSetupInterface $installer): Table
    {
        return $connection->newTable(
            $installer->getTable('channel_amazon_order_item')
        )->addColumn(
            'id',
            Table::TYPE_BIGINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'merchant_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'Merchant Id'
        )->addColumn(
            'order_id',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Order Id'
        )->addColumn(
            'order_item_id',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Order Item Id'
        )->addColumn(
            'qty_ordered',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Quantity Ordered'
        )->addColumn(
            'qty_shipped',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Quantity Shipped'
        )->addColumn(
            'title',
            Table::TYPE_TEXT,
            500,
            ['nullable' => false],
            'Order Item Title'
        )->addColumn(
            'sku',
            Table::TYPE_TEXT,
            256,
            ['nullable' => false],
            'Sku'
        )->addColumn(
            'asin',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'ASIN'
        )->addColumn(
            'condition',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Product Condition'
        )->addColumn(
            'subcondition',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Product Subcondition'
        )->addColumn(
            'item_price',
            Table::TYPE_DECIMAL,
            [12, 2],
            ['nullable' => false, 'default' => 0.0000],
            'Item Sale Price'
        )->addColumn(
            'item_tax',
            Table::TYPE_DECIMAL,
            [12, 2],
            ['nullable' => false, 'default' => 0.0000],
            'Item Sale Tax'
        )->addColumn(
            'shipping_price',
            Table::TYPE_DECIMAL,
            [12, 2],
            ['nullable' => false, 'default' => 0.0000],
            'Item Shipping Price'
        )->addColumn(
            'promotional_discount',
            Table::TYPE_DECIMAL,
            [12, 2],
            ['nullable' => false, 'default' => 0.0000],
            'Promotional Discount'
        )->addIndex(
            $installer->getIdxName('order_id', ['order_id']),
            ['order_id']
        )->addIndex(
            $installer->getIdxName(
                'channel_amazon_order_item',
                ['uniquevalue'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['order_item_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        );
    }

    /**
     * @param AdapterInterface $connection
     * @param SchemaSetupInterface $installer
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createOrderReserveTable(AdapterInterface $connection, SchemaSetupInterface $installer): Table
    {
        return $connection->newTable(
            $installer->getTable('channel_amazon_order_reserve')
        )->addColumn(
            'id',
            Table::TYPE_BIGINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'merchant_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'Merchant Id'
        )->addColumn(
            'order_id',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Order Id'
        )->addColumn(
            'order_item_id',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Order Item Id'
        )->addColumn(
            'qty',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Quantity Reserved'
        )->addColumn(
            'title',
            Table::TYPE_TEXT,
            500,
            ['nullable' => false],
            'Product Title'
        )->addColumn(
            'sku',
            Table::TYPE_TEXT,
            256,
            ['nullable' => false],
            'Sku'
        )->addColumn(
            'status',
            Table::TYPE_TEXT,
            30,
            ['nullable' => false],
            'Reserve Status'
        )->addColumn(
            'reserved_on',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Reserved On'
        )->addIndex(
            $installer->getIdxName('order_id', ['order_id']),
            ['order_id']
        )->addIndex(
            $installer->getIdxName(
                'channel_amazon_order_reserve',
                ['uniquevalue'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['order_item_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )
            ->addForeignKey(
                $installer->getFkName(
                    'channel_amazon_order_reserve',
                    'order_id',
                    'channel_amazon_order',
                    'order_id'
                ),
                'order_id',
                $installer->getTable('channel_amazon_order'),
                'order_id',
                Table::ACTION_CASCADE
            );
    }

    /**
     * @param $connection
     * @param $installer
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createOrderTrackingTable(AdapterInterface $connection, SchemaSetupInterface $installer): Table
    {
        return $connection->newTable(
            $installer->getTable('channel_amazon_order_tracking')
        )->addColumn(
            'id',
            Table::TYPE_BIGINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'merchant_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'Merchant Id'
        )->addColumn(
            'order_id',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Order Id'
        )->addColumn(
            'order_item_id',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Order Item Id'
        )->addColumn(
            'carrier_type',
            Table::TYPE_TEXT,
            200,
            ['nullable' => false],
            'Shipping Carrier Type'
        )->addColumn(
            'carrier_name',
            Table::TYPE_TEXT,
            200,
            ['nullable' => false],
            'Shipping Carrier Name'
        )->addColumn(
            'shipping_method',
            Table::TYPE_TEXT,
            200,
            ['nullable' => false],
            'Shipping Method'
        )->addColumn(
            'tracking_number',
            Table::TYPE_TEXT,
            200,
            ['nullable' => false],
            'Tracking Number'
        )->addColumn(
            'quantity',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Quantity Shipped'
        )->addIndex(
            $installer->getIdxName('order_id', ['order_id']),
            ['order_id']
        )
            ->addForeignKey(
                $installer->getFkName(
                    'channel_amazon_order_tracking',
                    'order_id',
                    'channel_amazon_order',
                    'order_id'
                ),
                'order_id',
                $installer->getTable('channel_amazon_order'),
                'order_id',
                Table::ACTION_CASCADE
            );
    }

    /**
     * @param AdapterInterface $connection
     * @param SchemaSetupInterface $installer
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createErrorLogTable(AdapterInterface $connection, SchemaSetupInterface $installer): Table
    {
        return $connection->newTable(
            $installer->getTable('channel_amazon_error_log')
        )->addColumn(
            'id',
            Table::TYPE_BIGINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'merchant_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'Merchant Id'
        )->addColumn(
            'error_code',
            Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Comm Error Code'
        )->addColumn(
            'message',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Error Message'
        )->addColumn(
            'created_on',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created On'
        )->addIndex(
            $installer->getIdxName('id', ['id']),
            ['id']
        )->addForeignKey(
            $installer->getFkName(
                'channel_amazon_error_log',
                'merchant_id',
                'channel_amazon_account',
                'merchant_id'
            ),
            'merchant_id',
            $installer->getTable('channel_amazon_account'),
            'merchant_id',
            Table::ACTION_CASCADE
        );
    }

    /**
     * @param AdapterInterface $connection
     * @param SchemaSetupInterface $installer
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createQuantityIndexTable(AdapterInterface $connection, SchemaSetupInterface $installer): Table
    {
        return $connection->newTable(
            $installer->getTable('channel_amazon_quantity_index')
        )->addColumn(
            'id',
            Table::TYPE_BIGINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'merchant_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'Merchant Id'
        )->addColumn(
            'seller_id',
            Table::TYPE_TEXT,
            40,
            ['nullable' => true],
            'Seller Id'
        )->addColumn(
            'parent_id',
            Table::TYPE_BIGINT,
            null,
            ['nullable' => false, 'unsigned' => true],
            'Parent Id'
        )->addColumn(
            'quantity',
            Table::TYPE_DECIMAL,
            [12],
            ['nullable' => false, 'default' => 0.0000],
            'Listing Quantity'
        )->addColumn(
            'seller_sku',
            Table::TYPE_TEXT,
            40,
            ['nullable' => true],
            'Seller SKU'
        )->addIndex(
            $installer->getIdxName(
                'channel_amazon_quantity_index',
                ['uniquevalue'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['seller_id', 'seller_sku'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $installer->getIdxName('id', ['id']),
            ['id']
        );
    }

    /**
     * @param AdapterInterface $connection
     * @param SchemaSetupInterface $installer
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createPricingIndexTable(AdapterInterface $connection, SchemaSetupInterface $installer): Table
    {
        return $connection->newTable(
            $installer->getTable('channel_amazon_pricing_index')
        )->addColumn(
            'id',
            Table::TYPE_BIGINT,
            null,
            ['identity' => true, 'nullable' => false, 'unsigned' => true, 'primary' => true],
            'Id'
        )->addColumn(
            'merchant_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'Merchant Id'
        )->addColumn(
            'parent_id',
            Table::TYPE_BIGINT,
            null,
            ['nullable' => false, 'unsigned' => true],
            'Parent Id'
        )->addColumn(
            'product_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Product Id'
        )->addColumn(
            'asin',
            Table::TYPE_TEXT,
            250,
            ['nullable' => true, 'default' => null],
            'ASIN'
        )->addColumn(
            'condition',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 11],
            'Amazon Condition'
        )->addColumn(
            'catalog_price',
            Table::TYPE_DECIMAL,
            [12, 2],
            ['nullable' => false, 'default' => 0.0000],
            'Magento Catalog Price'
        )->addColumn(
            'listing_price',
            Table::TYPE_DECIMAL,
            [12, 2],
            ['nullable' => false, 'default' => 0.0000],
            'Amazon Listing Price'
        )->addColumn(
            'shipping_price',
            Table::TYPE_DECIMAL,
            [12, 2],
            ['nullable' => false, 'default' => 0.0000],
            'Amazon Shipping Price'
        )->addColumn(
            'landed_price',
            Table::TYPE_DECIMAL,
            [12, 2],
            ['nullable' => false, 'default' => 0.0000],
            'Amazon Landed Price'
        )->addColumn(
            'floor_price',
            Table::TYPE_DECIMAL,
            [12, 2],
            ['nullable' => false, 'default' => 0.0000],
            'Amazon Floor Price (per rule)'
        )->addColumn(
            'stop_rules',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Stop Further Rules Processing'
        )->addColumn(
            'is_seller',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Is Seller'
        )->addColumn(
            'apply_vat',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 1],
            'Apply VAT Tax Flag'
        )->addColumn(
            'shipping_calculated',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Shiping Calculated Flag'
        )->addIndex(
            $installer->getIdxName('parent_id', ['parent_id']),
            ['parent_id']
        )->addIndex(
            $installer->getIdxName('id', ['id']),
            ['id']
        )->addIndex(
            $installer->getIdxName('asin', ['asin']),
            ['asin']
        )->addIndex(
            $installer->getIdxName('condition', ['condition']),
            ['condition']
        )->addIndex(
            $installer->getIdxName('shipping_calculated', ['shipping_calculated']),
            ['shipping_calculated']
        )->addIndex(
            $installer->getIdxname('stop_rules', ['stop_rules']),
            ['stop_rules']
        )->addIndex(
            $installer->getIdxName('product_id', ['product_id']),
            ['product_id']
        )->addIndex(
            $installer->getIdxName(
                'channel_amazon_pricing_index',
                ['uniquevalue'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['parent_id', 'merchant_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        );
    }

    /**
     * @param AdapterInterface $connection
     * @param SchemaSetupInterface $installer
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createLogProcessingTable(AdapterInterface $connection, SchemaSetupInterface $installer): Table
    {
        $tableName = $installer->getTable('channel_amazon_log_processing');
        return $connection->newTable(
            $tableName
        )->addColumn(
            'log_id',
            Table::TYPE_BIGINT,
            null,
            ['nullable' => false, 'unsigned' => true, 'primary' => false],
            'Id'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created At'
        )->addIndex(
            $installer->getIdxName($tableName, ['log_id']),
            ['log_id']
        )->addIndex(
            $installer->getIdxName($tableName, ['created_at']),
            ['created_at']
        );
    }
}
