<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageSuper\Casat\Setup;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\Data\CategoryAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\App\Config\Storage\WriterInterface;

/**
 * Upgrade Data script
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    private $categorySetupFactory;

    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    protected $objectmanager;
    protected $configWriter;

    /**
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * Init
     *
     * @param CategorySetupFactory $categorySetupFactory
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(CategorySetupFactory $categorySetupFactory,
                                EavSetupFactory $eavSetupFactory,
                                \Magento\Framework\ObjectManagerInterface $objectmanager,
                                WriterInterface $configWriter,
                                \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
    )
    {
        $this->categorySetupFactory = $categorySetupFactory;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->objectmanager = $objectmanager;
        $this->configWriter = $configWriter;
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.0.32') < 0) {
            // set new resource model paths
            /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $categorySetup->addAttribute(
                ProductAttributeInterface::ENTITY_TYPE_CODE,
                'frontend_stock',
                [
                    'group' => 'General',
                    'type' => 'int',
                    'backend' => '',
                    'label' => 'Frontend Stock Status',
                    'input' => 'select',
                    'source' => 'Magento\CatalogInventory\Model\Source\Stock',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'default' => \Magento\CatalogInventory\Model\Stock::STOCK_IN_STOCK,
                    'user_defined' => true,
                    'visible' => true,
                    'required' => false,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'unique' => false,
                    'sort_order' => 13
                ]
            );
        }
        if (version_compare($context->getVersion(), '0.0.33') < 0) {
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
            $collection = $this->objectmanager->get('\Magento\Catalog\Model\ResourceModel\Product\Collection');
            foreach ($collection as $product) {
                /** @var \Magento\Catalog\Model\Product $product */
                if (!in_array($product->getEntityId(), array(5212, 5314, 5488, 5549, 5589, 5590, 5611, 5633, 5702, 5901, 5959, 5960, 6043, 6105, 7183, 7284, 7444, 7626, 7659, 7662, 7663, 7699, 7710, 7711, 7729, 7809, 7810, 7813))) {
                    $product->setData('frontend_stock', 1);
                } else {
                    $product->setData('frontend_stock', 0);
                }
                $product->getResource()->saveAttribute($product, 'frontend_stock');
            }
        }
        if (version_compare($context->getVersion(), '0.0.34') < 0) {
            $setup->getConnection()->query('UPDATE catalog_product_entity_text SET value=REPLACE(value, \'&nbsp;|&nbsp;\', \' | \')');
        }

        if (version_compare($context->getVersion(), '0.0.35') < 0) {
            // set new resource model paths
            /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $categorySetup->addAttribute(
                CategoryAttributeInterface::ENTITY_TYPE_CODE,
                'description_below_faq',
                [
                    'type' => 'text',
                    'label' => 'Description below FAQ',
                    'input' => 'textarea',
                    'wysiwyg_enabled' => true,
                    'visible_on_front' => true,
                    'is_html_allowed_on_front' => true,
                    'required' => false,
                    'sort_order' => 100,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'group' => 'General Information',
                ]
            );
        }
        if (version_compare($context->getVersion(), '0.0.36') < 0) {
            // set new resource model paths
            /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $categorySetup->addAttribute(
                ProductAttributeInterface::ENTITY_TYPE_CODE,
                'alternate_url',
                [
                    'label' => 'Alternate Url',
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'input' => 'text',
                    'class' => '',
                    'source' => '',
                    'global' => 1,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => null,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => '',
                    'system' => 1,
                    'group' => 'General',
                    'sort_order' => 100
                ]
            );
        }
        if (version_compare($context->getVersion(), '0.0.37') < 0) {
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $categorySetup->addAttribute(
                ProductAttributeInterface::ENTITY_TYPE_CODE,
                'profit',
                [
                    'type' => 'decimal',
                    'label' => 'Profit',
                    'input' => 'price',
                    'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Price',
                    'required' => false,
                    'sort_order' => 150,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                    'used_in_product_listing' => false,
                    'group' => 'Advanced Pricing',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
            $categorySetup->addAttribute(
                ProductAttributeInterface::ENTITY_TYPE_CODE,
                'margin',
                [
                    'type' => 'decimal',
                    'label' => 'Margin',
                    'input' => 'price',
                    'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Price',
                    'required' => false,
                    'sort_order' => 155,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                    'used_in_product_listing' => false,
                    'group' => 'Advanced Pricing',
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false,
                ]
            );
        }
        if (version_compare($context->getVersion(), '0.0.38') < 0) {
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $categorySetup->removeAttribute(
                ProductAttributeInterface::ENTITY_TYPE_CODE,
                'a_gross_profit'
            );
            $categorySetup->removeAttribute(
                ProductAttributeInterface::ENTITY_TYPE_CODE,
                'a_gross_profit_percentage'
            );
        }
        if (version_compare($context->getVersion(), '0.0.40') < 0) {
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $categorySetup->updateAttribute(ProductAttributeInterface::ENTITY_TYPE_CODE, 'profit', 'backend_model', null);
            $categorySetup->updateAttribute(ProductAttributeInterface::ENTITY_TYPE_CODE, 'margin', 'backend_model', null);
        }

        if (version_compare($context->getVersion(), '0.0.46') < 0) {
            $this->configWriter->save('onestepcheckout/options_sort/sort_email', '2');
            $this->configWriter->save('onestepcheckout/options_sort/sort_company', '4');
            $this->configWriter->save('onestepcheckout/options_sort/sort_street', '5');
            $this->configWriter->save('onestepcheckout/options_sort/sort_city', '6');
            $this->configWriter->save('onestepcheckout/options_sort/sort_state', '6');
            $this->configWriter->save('onestepcheckout/options_sort/sort_zip', '7');
            $this->configWriter->save('onestepcheckout/options_sort/sort_country', '7');
            $this->configWriter->save('onestepcheckout/options_sort/sort_telephone', '8');
            $this->configWriter->save('onestepcheckout/options_sort/sort_fax', '8');
            $this->configWriter->save('onestepcheckout/options_sort/sort_dob', '9');
            $this->configWriter->save('onestepcheckout/options_sort/sort_gender', '10');
            $this->configWriter->save('onestepcheckout/options_sort/sort_taxvat', '10');
            $this->configWriter->save('onestepcheckout/options_sort/sort_password', '3');
            $this->configWriter->save('onestepcheckout/options_sort/sort_password_conf', '3');
        }
        if (version_compare($context->getVersion(), '0.0.47') < 0) {
            $customer_ids = $setup->getConnection()->fetchCol("SELECT entity_id from customer_entity where password_hash is null and created_at>'2017-08-01' and created_at<'2017-09-30'");
            $customer_ids = implode(',', $customer_ids);
            $data = array();
            if($customer_ids){
                $hashes = $setup->getConnection()->fetchAll("SELECT customer_id, password_hash from quote where created_at>'2017-08-01' and created_at<'2017-09-30' and password_hash is not null and customer_id in(" . $customer_ids . ")");

                foreach ($hashes as $hash) {
                    $data[] = '(' . $hash['customer_id'] . ',\'' . $hash['password_hash'] . '\')';
                }
            }
            if ($data) {
                $data = implode(',', $data);
                $setup->getConnection()->query("INSERT INTO customer_entity (entity_id,password_hash) values {$data} ON DUPLICATE KEY UPDATE password_hash=VALUES(password_hash)");
            }
        }
        if (version_compare($context->getVersion(), '0.0.48') < 0) {
            $this->configWriter->save('onestepcheckout/options_sort/sort_email', '1');
            $this->configWriter->save('onestepcheckout/options_sort/sort_company', '4');
            $this->configWriter->save('onestepcheckout/options_sort/sort_street', '5');
            $this->configWriter->save('onestepcheckout/options_sort/sort_city', '6');
            $this->configWriter->save('onestepcheckout/options_sort/sort_state', '6');
            $this->configWriter->save('onestepcheckout/options_sort/sort_zip', '7');
            $this->configWriter->save('onestepcheckout/options_sort/sort_country', '7');
            $this->configWriter->save('onestepcheckout/options_sort/sort_telephone', '8');
            $this->configWriter->save('onestepcheckout/options_sort/sort_fax', '8');
            $this->configWriter->save('onestepcheckout/options_sort/sort_dob', '9');
            $this->configWriter->save('onestepcheckout/options_sort/sort_gender', '10');
            $this->configWriter->save('onestepcheckout/options_sort/sort_taxvat', '10');
            $this->configWriter->save('onestepcheckout/options_sort/sort_password', '2');
            $this->configWriter->save('onestepcheckout/options_sort/sort_password_conf', '2');
            $this->configWriter->save('onestepcheckout/options_sort/sort_name', '3');
            $this->configWriter->save('onestepcheckout/options_sort/sort_vat_id', '11');
            $this->configWriter->save('onestepcheckout/addfield/price_gift_wrap', '');

        }
        if (version_compare($context->getVersion(), '0.0.49') < 0) {
            $customer_ids = $setup->getConnection()->fetchCol("SELECT entity_id from customer_entity");
            $data = array();
            foreach ($customer_ids as $customer_id) {
                $data[] = '(' . $customer_id . ',\'' . $customer_id . '\')';
            }
            if ($data) {
                $data = implode(',', $data);
                $setup->getConnection()->query("INSERT INTO customer_entity (entity_id,increment_id) values {$data} ON DUPLICATE KEY UPDATE increment_id=VALUES(increment_id)");
            }
        }

        if (version_compare($context->getVersion(), '0.0.51', '<')) {
            /** @var  \Magento\Customer\Setup\CustomerSetup $customerSetup */
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            $customerSetup->addAttribute('customer', 'old_id', array(
                'type' => 'static',
                'label' => 'Old id',
                'input' => 'text',
                'required' => false,
                'adminhtml_only' => true
            ));
        }
        if (version_compare($context->getVersion(), '0.0.52', '<')) {
            /** @var  \Magento\Customer\Setup\CustomerSetup $customerSetup */
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            $customerSetup->addAttribute('customer', 'id_prefix', array(
                'type' => 'static',
                'label' => 'id Prefix',
                'input' => 'text',
                'required' => false,
                'adminhtml_only' => true
            ));
        }
        if (version_compare($context->getVersion(), '0.0.52', '<')) {
            /** @var  \Magento\Customer\Setup\CustomerSetup $customerSetup */
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            $customerSetup->addAttribute('customer', 'id_prefix', array(
                'type' => 'static',
                'label' => 'id Prefix',
                'input' => 'text',
                'required' => false,
                'adminhtml_only' => true
            ));
        }
        if (version_compare($context->getVersion(), '0.0.53', '<')) {
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('customer_entity'),
                    'id_prefix',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'length' => '255',
                        'comment' => 'id_prefix'
                    ]
                );
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('customer_entity'),
                    'old_id',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'length' => '255',
                        'comment' => 'old id'
                    ]
                );

        }
        if (version_compare($context->getVersion(), '0.0.54', '<')) {
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('sales_order'),
                    'old_id',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'length' => '255',
                        'comment' => 'id_prefix'
                    ]
                );

        }
        if (version_compare($context->getVersion(), '0.0.55', '<')) {
            $ups = $setup->getConnection()
                ->fetchAll(
                    "SELECT entity_id,old_id from {$setup->getTable('sales_order')} WHERE old_id is not null and entity_id>=36845 and entity_id<=36971"
                );
            $update = [];
            foreach ($ups as $up) {
                $update[] = '(' . $up['entity_id'] . ',' . '\'USA1101' . $up['old_id'] . '\')';
            }
            $update = implode(',', $update);
            if($update){
                $setup->getConnection()->query("INSERT INTO {$setup->getTable('sales_order')} (entity_id,increment_id) VALUES {$update} ON DUPLICATE KEY UPDATE increment_id=VALUES(increment_id)");
            }

            $ups = $setup->getConnection()
                ->fetchAll(
                    "SELECT entity_id,old_id from {$setup->getTable('sales_order')} WHERE old_id is not null and entity_id>=36972 and entity_id<=38043"
                );
            $update = [];
            foreach ($ups as $up) {
                $update[] = '(' . $up['entity_id'] . ',' . '\'YYC' . $up['old_id'] . '\')';
            }
            $update = implode(',', $update);
            if($update) {
                $setup->getConnection()->query("INSERT INTO {$setup->getTable('sales_order')} (entity_id,increment_id) VALUES {$update} ON DUPLICATE KEY UPDATE increment_id=VALUES(increment_id)");
            }

        }
        if (version_compare($context->getVersion(), '0.0.56', '<')) {
            $ups = $setup->getConnection()
                ->fetchAll(
                    "SELECT entity_id,customer_email from sales_order WHERE old_id is not null and customer_id is null"
                );
            if ($ups) {
                $emails = [];
                foreach ($ups as $up) {
                    $emails[] = "'" . $up['customer_email'] . "'";
                }
                $emails = array_unique($emails);
                $emails = implode(',', $emails);
                $customers = $setup->getConnection()
                    ->fetchAll(
                        "SELECT entity_id,email from customer_entity WHERE email in({$emails})"
                    );
                foreach ($customers as $customer) {
                    foreach ($ups as $up) {
                        if ($up['customer_email'] == $customer['email']) {
                            $setup->getConnection()
                                ->query(
                                    "update sales_order set customer_id={$customer['entity_id']} WHERE entity_id={$up['entity_id']}"
                                );
                            $setup->getConnection()
                                ->query(
                                    "update sales_order_grid set customer_id={$customer['entity_id']} WHERE entity_id={$up['entity_id']}"
                                );
                        }
                    }
                }
            }

        }

        if (version_compare($context->getVersion(), '0.0.57', '<')) {
            $ups = $setup->getConnection()
                ->fetchAll(
                    "SELECT entity_id,old_id from {$setup->getTable('sales_order')} WHERE old_id is not null and entity_id>=36845 and entity_id<=36971"
                );
            $update = [];
            foreach ($ups as $up) {
                $update[] = '(' . $up['entity_id'] . ',' . '\'USA1101' . $up['old_id'] . '\')';
            }
            $update = implode(',', $update);
            if ($update) {
                $setup->getConnection()->query("INSERT INTO sales_order_grid (entity_id,increment_id) VALUES {$update} ON DUPLICATE KEY UPDATE increment_id=VALUES(increment_id)");
            }
            $ups = $setup->getConnection()
                ->fetchAll(
                    "SELECT entity_id,old_id from {$setup->getTable('sales_order')} WHERE old_id is not null and entity_id>=36972 and entity_id<=38043"
                );
            $update = [];
            foreach ($ups as $up) {
                $update[] = '(' . $up['entity_id'] . ',' . '\'YYC' . $up['old_id'] . '\')';
            }
            $update = implode(',', $update);
            if($update){
                $setup->getConnection()->query("INSERT INTO sales_order_grid (entity_id,increment_id) VALUES {$update} ON DUPLICATE KEY UPDATE increment_id=VALUES(increment_id)");
            }

        }

        if (version_compare($context->getVersion(), '0.0.58', '<')) {
            /*$customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            $customerSetup->addAttribute('customer_grid_flat','old_id',array(
                'type' => 'static',
                'label' => 'Old Id',
                'input' => 'text',
                'required' => false,
                'adminhtml_only' => true
            ));*/
            $setup->getConnection()
                ->query(
                    "UPDATE customer_entity set website_id=7 WHERE id_prefix='USA1101'"
                );

            $setup->getConnection()
                ->query(
                    "UPDATE customer_entity set website_id=6 WHERE id_prefix='YYC'"
                );
        }
        if (version_compare($context->getVersion(), '0.0.59', '<')) {

            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('customer_grid_flat'),
                    'old_id',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'length' => '255',
                        'comment' => 'old id'
                    ]
                );

        }
        if (version_compare($context->getVersion(), '0.0.60', '<')) {

            $data = $setup->getConnection()
                ->fetchAll(
                    "SELECT item_id,row_total,price,order_id,row_invoiced FROM sales_order_item where qty_ordered=0"
                );
            $orderId = [];
            foreach ($data as $item) {
                $orderId[] = $item['order_id'];
            }
            $orderIds = implode(',', $orderId);
            $orders_data = $setup->getConnection()
                ->fetchAll(
                    "SELECT entity_id,grand_total,total_paid FROM sales_order WHERE entity_id in ({$orderIds})"
                );
            foreach ($data as $item) {
                if ($item['price'] > 0) {
                    $qty = round($item['row_total'] / $item['price']);
                } else {
                    $qty = 1;
                }
                $totaly_paid = false;
                foreach ($orders_data as $order_data) {
                    if ($order_data['entity_id'] == $item['order_id']) {
                        if ($order_data['grand_total'] == $order_data['total_paid']) {
                            $totaly_paid = true;
                            break;
                        }
                    }
                }
                $qty_invoiced = 0;
                $row_invoided = 0;
                if($totaly_paid){
                    $qty_invoiced = $qty;
                    $row_invoided = $item['row_total'];
                }

                $setup->getConnection()
                    ->query(
                        "INSERT INTO sales_order_item (item_id,qty_ordered,qty_invoiced,row_invoiced) VALUES ({$item['item_id']},{$qty},{$qty_invoiced},{$row_invoided}) ON DUPLICATE KEY UPDATE qty_ordered=VALUES(qty_ordered),qty_invoiced=VALUES(qty_invoiced),row_invoiced=VALUES(row_invoiced) "
                    );
            }

        }
        if (version_compare($context->getVersion(), '0.0.61', '<')) {

            $data = $setup->getConnection()
                ->fetchAll(
                    "SELECT item_id,row_total FROM sales_order_item where row_invoiced=0 and qty_invoiced=qty_ordered"
                );
            $update = [];
            foreach ($data as $item) {
                if($item['row_total']>0){
                    $update[] = '('.$item['item_id'].','.$item['row_total'].')';
                }
            }
            $update = implode(',', $update);
            if($update){
                $setup->getConnection()
                    ->query(
                        "INSERT INTO sales_order_item (item_id,row_invoiced) VALUES {$update} ON DUPLICATE KEY UPDATE row_invoiced=VALUES(row_invoiced) "
                    );
            }

        }
        if (version_compare($context->getVersion(), '0.0.63', '<')) {

            $setup->getConnection()->beginTransaction();
            try {
                $setup->getConnection()->query('UPDATE os_supplier SET supplier_currrency="CAD" WHERE country_id="CA"');
                $setup->getConnection()->query('UPDATE os_supplier SET supplier_currrency="USD" WHERE country_id="US"');
                $setup->getConnection()->commit();
            } catch (\Exception $e) {
                $setup->getConnection()->rollBack();
                throw $e;
            }
        }

        if (version_compare($context->getVersion(), '0.0.64', '<')) {

            $setup->getConnection()->beginTransaction();
            try {
                $setup->getConnection()->query('UPDATE os_purchase_order_code SET current_id="5207" WHERE code="PO"');
                $setup->getConnection()->commit();
            } catch (\Exception $e) {
                $setup->getConnection()->rollBack();
                throw $e;
            }
        }

        if (version_compare($context->getVersion(), '0.0.66', '<')) {

            $setup->getConnection()->beginTransaction();
            try {
                $setup->getConnection()->query('UPDATE eav_attribute SET attribute_code="attr_vendor_description" WHERE attribute_id="245"');
                $setup->getConnection()->query('UPDATE eav_attribute SET attribute_code="attr_vendor_part" WHERE attribute_id="229"');
                $attrToHide = '228,245,241,242,243,244,248,249,250,251,252';
                $setup->getConnection()->query("UPDATE catalog_eav_attribute SET is_visible='0' WHERE attribute_id IN({$attrToHide})");
                $setup->getConnection()->commit();
            } catch (\Exception $e) {
                $setup->getConnection()->rollBack();
                throw $e;
            }
        }

        if (version_compare($context->getVersion(), '0.0.68', '<')) {

            $setup->getConnection()->beginTransaction();
            try {
                $ids = '228,241,242,243,244,248,249,250,251,252';
                $setup->getConnection()->query("DELETE FROM eav_attribute WHERE attribute_id IN({$ids})");

                $setup->getConnection()->commit();
            } catch (\Exception $e) {
                $setup->getConnection()->rollBack();
                throw $e;
            }
        }
        if (version_compare($context->getVersion(), '0.0.69', '<')) {

            $setup->getConnection()->beginTransaction();
            try {
                $ids = '228,241,242,243,244,248,249,250,251,252';
                $setup->getConnection()->query("DELETE FROM catalog_product_entity_datetime WHERE attribute_id IN({$ids})");
                $setup->getConnection()->query("DELETE FROM catalog_product_entity_decimal WHERE attribute_id IN({$ids})");
                $setup->getConnection()->query("DELETE FROM catalog_product_entity_int WHERE attribute_id IN({$ids})");
                $setup->getConnection()->query("DELETE FROM catalog_product_entity_text WHERE attribute_id IN({$ids})");
                $setup->getConnection()->query("DELETE FROM catalog_product_entity_varchar WHERE attribute_id IN({$ids})");

                $setup->getConnection()->commit();
            } catch (\Exception $e) {
                $setup->getConnection()->rollBack();
                throw $e;
            }
        }
        if (version_compare($context->getVersion(), '0.0.70', '<')) {
            $catalogSetup = $this->categorySetupFactory->create(['setup' => $setup]);

            $groupName = 'Product Details';
            $entityTypeId = $catalogSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
            $attributeSetId = $catalogSetup->getAttributeSetId($entityTypeId, 'Default');

            /* Add hide price attribute */
            $catalogSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'cart2quote_quotable',
                [
                    'group' => $groupName,
                    'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Boolean',
                    'frontend' => '',
                    'label' => 'Quotable',
                    'input' => 'select',
                    'class' => '',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '',
                    'apply_to' => '',
                    'input_renderer' => 'Cart2Quote\Quotation\Block\Adminhtml\Form\QuotableConfig',
                    'visible_on_front' => false,
                ]
            );
            $attribute = $catalogSetup->getAttribute($entityTypeId, 'cart2quote_quotable');
            if ($attribute) {
                $catalogSetup->addAttributeToGroup(
                    $entityTypeId,
                    $attributeSetId,
                    $groupName,
                    $attribute['attribute_id'],
                    22
                );
            }
        }
        if (version_compare($context->getVersion(), '0.0.73') < 0) {
            // set new resource model paths
            /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $categorySetup->addAttribute(
                ProductAttributeInterface::ENTITY_TYPE_CODE,
                'quote_hide_price',
                [
                    'group' => 'General',
                    'type' => 'int',
                    'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Boolean',
                    'frontend' => '',
                    'label' => 'Hide Price & Addtocart button',
                    'input' => 'boolean',
                    'class' => '',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '0',
                    'apply_to' => '',
                    'visible_on_front' => false,
                    'used_in_product_listing' => true
                ]
            );
        }
        if (version_compare($context->getVersion(), '0.0.74') < 0) {
            $this->configWriter->save('cart2quote_quotation/proposal/auto_proposal', '1');
        }
        if (version_compare($context->getVersion(), '0.0.75') < 0) {
            $this->configWriter->save('cart2quote_quotation/global/show_sidebar', '0');
        }
        if (version_compare($context->getVersion(), '0.0.76') < 0) {
            $this->configWriter->save('sales/identity/address', <<<EOT
215 4th Street N.E.
Calgary, Alberta Canada,
T2E 3S1
EOT
);
        }
        if (version_compare($context->getVersion(), '0.0.77') < 0) {
            /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $categorySetup->updateAttribute('catalog_category', 'volusion_url', 'is_global',\Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE);
            $categorySetup->updateAttribute('catalog_category', 'alternate_url', 'is_global',\Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE);
            $categorySetup->updateAttribute('catalog_category', 'manual_title', 'is_global',\Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE);
            $categorySetup->updateAttribute('catalog_category', 'secondary_description', 'is_global',\Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE);
            $categorySetup->updateAttribute('catalog_category', 'description_below_faq', 'is_global',\Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE);
            $categorySetup->updateAttribute('catalog_category', 'noofsubcategoriesmobile', 'is_global',\Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE);
            $categorySetup->updateAttribute('catalog_category', 'noofsubcategories', 'is_global',\Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE);
        }

        if (version_compare($context->getVersion(), '0.0.78') < 0) {
            $setup->getConnection()->beginTransaction();
            try {
                $setup->getConnection()->query("UPDATE sales_sequence_profile set prefix='Q' WHERE prefix='Q15.'");
                $setup->getConnection()->commit();
            } catch (\Exception $e) {
                $setup->getConnection()->rollBack();
                throw $e;
            }
        }
        if (version_compare($context->getVersion(), '0.0.79') < 0) {
            $setup->getConnection()->beginTransaction();
            try {
                $setup->getConnection()->query("INSERT INTO sequence_quote_0 VALUES (5002)");
                $setup->getConnection()->commit();
            } catch (\Exception $e) {
                $setup->getConnection()->rollBack();
                throw $e;
            }
        }
        if (version_compare($context->getVersion(), '0.0.80') < 0) {
            $setup->getConnection()->beginTransaction();
            try {
                $setup->getConnection()->query("UPDATE eav_entity_type SET increment_pad_length=5 WHERE entity_type_code='quote'");
                $setup->getConnection()->commit();
            } catch (\Exception $e) {
                $setup->getConnection()->rollBack();
                throw $e;
            }
        }
        if (version_compare($context->getVersion(), '0.0.81') < 0) {
            $setup->getConnection()->beginTransaction();
            try {
                $setup->getConnection()->query("UPDATE eav_attribute SET default_value=NULL WHERE attribute_code in('quote_hide_price','usd_is_base_price')");
                $setup->getConnection()->commit();
            } catch (\Exception $e) {
                $setup->getConnection()->rollBack();
                throw $e;
            }
        }
        if (version_compare($context->getVersion(), '0.0.82') < 0) {
            $setup->getConnection()->beginTransaction();
            try {
                $ids = $setup->getConnection()->fetchCol("SELECT entity_id from catalog_product_entity");
                $values = [];
                foreach($ids as $id){
                    $values[] = '(269,0,'.$id.',0)';
                }
                $v = implode(',',$values);
                $setup->getConnection()->query("INSERT IGNORE into catalog_product_entity_int (attribute_id,store_id,entity_id,value) VALUES $v");
                $setup->getConnection()->commit();
            } catch (\Exception $e) {
                $setup->getConnection()->rollBack();
                throw $e;
            }
        }

        if (version_compare($context->getVersion(), '0.0.83', '<')) {
            $installer = $setup;
            $installer->getConnection()->beginTransaction();
            try {
                $valuesAll = $installer->getConnection()->fetchAll('SELECT o.entity_id,o.margin,o.profit from sales_order as o WHERE o.profit is not NULL');
                $update = [];
                foreach ($valuesAll as $item) {
                    $update[$item['entity_id']] = '(' . $item['entity_id'] . ',"' .$item['profit']. '","'.$item['margin'].'")';
                }
                $values = implode(',', $update);

                if($values){
                    $installer->getConnection()->query('INSERT INTO sales_order_grid (entity_id, profit, margin) VALUES ' . $values . ' ON DUPLICATE KEY UPDATE profit=VALUES(profit), margin=VALUES(margin)');

                }
                $installer->getConnection()->commit();
            } catch (\Exception $e) {
                $installer->getConnection()->rollBack();
                throw $e;
            }
        }
        if (version_compare($context->getVersion(), '0.0.84', '<')) {
            $this->configWriter->save('customer/address_templates/pdf', <<<EOT
{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}|
{{depend company}}{{var company}}|{{/depend}}
{{if street1}}{{var street1}}
{{/if}}
{{depend street2}}{{var street2}}|{{/depend}}
{{depend street3}}{{var street3}}|{{/depend}}
{{depend street4}}{{var street4}}|{{/depend}}|
{{if city}}{{var city}},|{{/if}}
{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}|
{{var country}}|
{{depend telephone}}T: {{var telephone}}{{/depend}}|
{{depend fax}}<br/>F: {{var fax}}{{/depend}}|
{{depend vat_id}}<br/>VAT: {{var vat_id}}{{/depend}}|
EOT

                );
        }
        if (version_compare($context->getVersion(), '0.0.85') < 0) {
            $this->configWriter->save('sales/identity/address', <<<EOT
2121 39 Avenue NE, Unit H
Calgary, AB T2E 6R7
Canada
EOT
            );
        }
        if (version_compare($context->getVersion(), '0.0.86', '<')) {
            $installer = $setup;
            $installer->getConnection()->beginTransaction();
            try {
                $data = $installer->getConnection()->fetchAll('SELECT s.product_id,v.value from catalog_product_bundle_selection as s LEFT JOIN catalog_product_entity_decimal as v ON s.product_id=v.entity_id and v.attribute_id=77 and v.store_id=0 WHERE s.selection_price_value=0 AND v.value!=0');
                $update = [];
                foreach ($data as $item) {
                    $installer->getConnection()->query("UPDATE catalog_product_bundle_selection SET selection_price_value={$item['value']} WHERE product_id={$item['product_id']} AND selection_price_value=0");
                }
                $installer->getConnection()->commit();
            } catch (\Exception $e) {
                $installer->getConnection()->rollBack();
                throw $e;
            }
        }

        if (version_compare($context->getVersion(), '0.0.87') < 0) {
            $this->configWriter->save('alsobought_section/slider_settings/slider_loop', 'false'
            );
        }

        if (version_compare($context->getVersion(), '0.0.88', '<')) {
            $this->configWriter->save('customer/address_templates/pdf', <<<EOT
{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}|
{{depend company}}{{var company}}|{{/depend}}
{{if street1}}{{var street1}}
{{/if}}
{{depend street2}}{{var street2}}|{{/depend}}
{{depend street3}}{{var street3}}|{{/depend}}
{{depend street4}}{{var street4}}|{{/depend}}|
{{if city}}{{var city}}, {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}|
{{var country}}|
{{depend telephone}}T: {{var telephone}}{{/depend}}|
{{depend fax}}<br/>F: {{var fax}}{{/depend}}|
{{depend vat_id}}<br/>VAT: {{var vat_id}}{{/depend}}|
EOT

            );
        }
        if (version_compare($context->getVersion(), '0.0.90') < 0) {
            $this->configWriter->save('md_subscribenow/general/allow_guest_customer', '1'
            );
        }
        if (version_compare($context->getVersion(), '0.0.91') < 0) {
            $this->configWriter->save('payment/md_firstdata/active', '1'
            );
        }
        if (version_compare($context->getVersion(), '0.0.94') < 0) {
            /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $categorySetup->updateAttribute('catalog_product', 'alternate_url', 'is_global',\Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE);
        }
        if (version_compare($context->getVersion(), '0.0.95') < 0) {
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $categorySetup->updateAttribute(ProductAttributeInterface::ENTITY_TYPE_CODE, 'profit', 'backend_model', 'MageSuper\Casat\Model\Product\Attribute\Backend\Profit');
            $categorySetup->updateAttribute(ProductAttributeInterface::ENTITY_TYPE_CODE, 'margin', 'backend_model', 'MageSuper\Casat\Model\Product\Attribute\Backend\Profit');
        }

        $setup->endSetup();
    }

}
