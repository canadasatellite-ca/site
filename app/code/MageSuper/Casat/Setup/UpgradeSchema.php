<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageSuper\Casat\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Catalog\Setup\CategorySetupFactory;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $configWriter;

    protected $objectmanager;
    protected $categorySetupFactory;
    /**
     *
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    function __construct(
        WriterInterface $configWriter,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        CategorySetupFactory $categorySetupFactory
    )
    {
        $this->categorySetupFactory = $categorySetupFactory;
        $this->objectmanager = $objectmanager;
        $this->configWriter = $configWriter;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '0.0.2', '<')) {
            $installer->getConnection()
                ->addColumn(
                    $installer->getTable('mageworx_optiontemplates_group_option_type_value'),
                    'children',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'lenght' => '64k',
                        'nullable' => false,
                        'default' => '',
                        'comment' => 'Children',
                    ]
                );

        }

        if (version_compare($context->getVersion(), '0.0.3', '<')) {
            $installer->getConnection()
                ->addColumn(
                    $installer->getTable('mageworx_optiontemplates_group_option_type_value'),
                    'row_id',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'nullable' => true,
                        'default' => null,
                        'comment' => 'Row id',
                    ]
                );

        }

        if (version_compare($context->getVersion(), '0.0.4', '<')) {
            $this->configWriter->save('catalog/custom_options/year_range', '2017,2021');
        }

        if (version_compare($context->getVersion(), '0.0.5', '<')) {
            $installer->getConnection()->beginTransaction();
            try {
                $max_id = $installer->getConnection()->fetchOne('SELECT MAX(entity_id) from customer_entity');
                $installer->getConnection()->query('UPDATE customer_entity SET entity_id = entity_id + 44 WHERE entity_id>=11638 ORDER BY entity_id DESC');
                $installer->getConnection()->query('UPDATE customer_address_entity SET parent_id = parent_id + 44 WHERE parent_id>=11638');
                $installer->getConnection()->query('UPDATE sales_order SET customer_id = customer_id + 44 WHERE customer_id>=11638');
                $installer->getConnection()->commit();
            } catch (\Exception $e) {
                $installer->getConnection()->rollBack();
                throw $e;
            }
            $installer->getConnection()->query('ALTER TABLE customer_entity AUTO_INCREMENT=' . ($max_id) . ';');

        }

        if (version_compare($context->getVersion(), '0.0.6', '<')) {
            $installer->getConnection()->beginTransaction();
            try {
                $entity_ids = $installer->getConnection()->fetchCol('select entity_id from sales_order where length(increment_id)>6;');
                $new_data = array();
                $from = 110988;

                foreach ($entity_ids as $entity_id) {
                    $new_data[] = '(' . $entity_id . ',' . $from++ . ')';
                }
                $values = implode(',', $new_data);
                if ($values) {
                    $installer->getConnection()->query('INSERT INTO sales_order (entity_id,increment_id) VALUES ' . $values . ' ON DUPLICATE KEY UPDATE increment_id=VALUES(increment_id)');
                }
                $installer->getConnection()->commit();
            } catch (\Exception $e) {
                $installer->getConnection()->rollBack();
                throw $e;
            }
            $from = (string)$from;
            $from = substr($from, 1);
            $installer->getConnection()->query('ALTER TABLE sequence_order_1 AUTO_INCREMENT=' . ($from) . ';');
        }

        if (version_compare($context->getVersion(), '0.0.7', '<')) {
            /*$installer->getConnection()->beginTransaction();
            try{
                $entity_ids = $installer->getConnection()->fetchCol('select entity_id from sales_order_grid where length(increment_id)>6;');
                $new_data = array();
                $from = 110988;

                foreach($entity_ids as $entity_id){
                    $new_data[] = '('.$entity_id.','.$from++.')';
                }
                $values = implode(',',$new_data);
                if($values){
                    $installer->getConnection()->query('INSERT INTO sales_order_grid (entity_id,increment_id) VALUES '.$values.' ON DUPLICATE KEY UPDATE increment_id=VALUES(increment_id)');
                }
                $installer->getConnection()->commit();
            } catch (\Exception $e){
                $installer->getConnection()->rollBack();
                throw $e;
            }*/
        }

        if (version_compare($context->getVersion(), '0.0.8', '<')) {
            $installer->getConnection()->beginTransaction();
            try {
                $installer->getConnection()->query('UPDATE customer_entity SET entity_id = entity_id + 318 WHERE entity_id>=11682 ORDER BY entity_id DESC');
                $installer->getConnection()->query('UPDATE customer_address_entity SET parent_id = parent_id + 318 WHERE parent_id>=11682');
                $installer->getConnection()->query('UPDATE sales_order SET customer_id = customer_id + 318 WHERE customer_id>=11682');
                $max_id = $installer->getConnection()->fetchOne('SELECT MAX(entity_id) from customer_entity');
                $installer->getConnection()->commit();
            } catch (\Exception $e) {
                $installer->getConnection()->rollBack();
                throw $e;
            }
            $installer->getConnection()->query('ALTER TABLE customer_entity AUTO_INCREMENT=' . ($max_id) . ';');

        }
        if (version_compare($context->getVersion(), '0.0.9', '<')) {
            $installer->getConnection()->beginTransaction();
            try {
                $entity_ids = $installer->getConnection()->fetchCol('select entity_id from sales_order where length(increment_id)>6;');
                $new_data = array();
                $from = 111018;

                foreach ($entity_ids as $entity_id) {
                    $new_data[] = '(' . $entity_id . ',' . $from++ . ')';
                }
                $values = implode(',', $new_data);
                if ($values) {
                    $installer->getConnection()->query('INSERT INTO sales_order (entity_id,increment_id) VALUES ' . $values . ' ON DUPLICATE KEY UPDATE increment_id=VALUES(increment_id)');
                    $installer->getConnection()->query('INSERT INTO sales_order_grid (entity_id,increment_id) VALUES ' . $values . ' ON DUPLICATE KEY UPDATE increment_id=VALUES(increment_id)');
                }
                $installer->getConnection()->commit();
            } catch (\Exception $e) {
                $installer->getConnection()->rollBack();
                throw $e;
            }
            $from = (string)$from;
            $from = substr($from, 1);
            $installer->getConnection()->query('DELETE from sequence_order_1 WHERE sequence_value>' . ($from) . ';');
            $installer->getConnection()->query('ALTER TABLE sequence_order_1 AUTO_INCREMENT=' . ($from) . ';');
        }

        if (version_compare($context->getVersion(), '0.0.10', '<')) {
            $installer->getConnection()->beginTransaction();
            try {
                $installer->getConnection()->query('UPDATE customer_entity SET entity_id = entity_id - 37 WHERE entity_id>=12043 ORDER BY entity_id DESC');
                $installer->getConnection()->query('UPDATE customer_address_entity SET parent_id = parent_id - 37 WHERE parent_id>=12043');
                $installer->getConnection()->query('UPDATE sales_order SET customer_id = customer_id - 37 WHERE customer_id>=12043');
                $max_id = $installer->getConnection()->fetchOne('SELECT MAX(entity_id) from customer_entity');
                $installer->getConnection()->commit();
            } catch (\Exception $e) {
                $installer->getConnection()->rollBack();
                throw $e;
            }
            $installer->getConnection()->query('ALTER TABLE customer_entity AUTO_INCREMENT=' . ($max_id) . ';');

        }

        if (version_compare($context->getVersion(), '0.0.11', '<')) {
            $this->configWriter->save('payment/beanstream/payment_action', 'authorize_capture');
        }

        if (version_compare($context->getVersion(), '0.0.12', '<')) {
            $installer->getConnection()->beginTransaction();
            try {
                $installer->getConnection()->query('DELETE from customer_entity WHERE created_at>"2017-05-23" and created_at<"2017-06-17"');
                $installer->getConnection()->query('DELETE from sales_order WHERE created_at>"2017-05-23" and entity_id<=32676');
                $installer->getConnection()->query('DELETE from sales_order_grid WHERE created_at>"2017-05-23" and entity_id<=32676');

                $ids = $installer->getConnection()->fetchAll('select entity_id,increment_id from sales_order where store_id=1 order by entity_id asc');
                $new_from = 110991;
                $new_data = [];
                $sequence = [];
                foreach ($ids as $id) {
                    if ($id['entity_id'] > 32676) {
                        $new_increment_id = $new_from++;
                        $erese_data[] = '(' . $id['entity_id'] . ',' . '1234' . $new_increment_id . ')';
                        $new_data[] = '(' . $id['entity_id'] . ',' . $new_increment_id . ')';

                        $from = (string)$new_increment_id;
                    } else {
                        $from = (string)$id['increment_id'];
                    }
                    $from = substr($from, 1);
                    $sequence[] = '(' . $from . ')';
                }
                $values = implode(',', $new_data);
                $erese_data = implode(',', $erese_data);
                if ($values) {
                    $installer->getConnection()->query('INSERT INTO sales_order (entity_id,increment_id) VALUES ' . $erese_data . ' ON DUPLICATE KEY UPDATE increment_id=VALUES(increment_id)');
                    $installer->getConnection()->query('INSERT INTO sales_order_grid (entity_id,increment_id) VALUES ' . $erese_data . ' ON DUPLICATE KEY UPDATE increment_id=VALUES(increment_id)');

                    $installer->getConnection()->query('INSERT INTO sales_order (entity_id,increment_id) VALUES ' . $values . ' ON DUPLICATE KEY UPDATE increment_id=VALUES(increment_id)');
                    $installer->getConnection()->query('INSERT INTO sales_order_grid (entity_id,increment_id) VALUES ' . $values . ' ON DUPLICATE KEY UPDATE increment_id=VALUES(increment_id)');
                }

                $from = (string)$new_increment_id;
                $from = substr($from, 1);
                $sequence = implode(',', $sequence);
                $installer->getConnection()->query('DELETE from sequence_order_1;');
                $installer->getConnection()->query('INSERT INTO sequence_order_1 (sequence_value) VALUES ' . $sequence . ' ON DUPLICATE KEY UPDATE sequence_value=VALUES(sequence_value)');

                $installer->getConnection()->commit();
            } catch (\Exception $e) {
                $installer->getConnection()->rollBack();
                throw $e;
            }
            $from = (string)$new_from;
            $from = substr($from, 1);
            $installer->getConnection()->query('ALTER TABLE sequence_order_1 AUTO_INCREMENT=' . ($from) . ';');
        }
        if (version_compare($context->getVersion(), '0.0.13', '<')) {
            $this->configWriter->save('carriers/freeshipping/active', '0');
            $this->configWriter->save('carriers/freeshippingcustom/active', '1');
            $this->configWriter->save('carriers/freeshippingcustom/name', 'Delivery');
            $this->configWriter->save('carriers/freeshippingcustom/title', 'Free');
        }

        if (version_compare($context->getVersion(), '0.0.14', '<')) {
            $installer->getConnection()->beginTransaction();
            try {
                $installer->getConnection()->query('DELETE from customer_form_attribute WHERE attribute_id in (22,24,26)
                    AND form_code IN("adminhtml_customer_address","customer_address_edit","customer_register_address")');
                $installer->getConnection()->query('UPDATE eav_attribute SET frontend_label="Alternate number" WHERE attribute_id=35');

                $installer->getConnection()->commit();
            } catch (\Exception $e) {
                $installer->getConnection()->rollBack();
                throw $e;
            }
        }

        if (version_compare($context->getVersion(), '0.0.15', '<')) {
            $installer->getConnection()
                ->addColumn(
                    $installer->getTable('mageworx_optiontemplates_group_option_title'),
                    'description',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'lenght' => '64k',
                        'nullable' => false,
                        'default' => '',
                        'comment' => 'Description',
                    ]
                );
            $installer->getConnection()
                ->addColumn(
                    $installer->getTable('catalog_product_option_title'),
                    'description',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'lenght' => '64k',
                        'nullable' => false,
                        'default' => '',
                        'comment' => 'Description',
                    ]
                );

        }

        if (version_compare($context->getVersion(), '0.0.16', '<')) {
            $ids = $installer->getConnection()->fetchCol('select v.option_type_id from mageworx_optiontemplates_group_option_type_value as v
                LEFT JOIN mageworx_optiontemplates_group_option_type_title as t on v.option_type_id = t.option_type_id
                where t.option_type_id is null');
            if ($ids) {
                $installer->getConnection()->query('delete from mageworx_optiontemplates_group_option_type_value where option_type_id in(' . implode(',', $ids) . ')');
            }
        }
        if (version_compare($context->getVersion(), '0.0.17', '<')) {
            $ids = $installer->getConnection()->fetchCol('select v.option_type_id from catalog_product_option_type_value as v
                  LEFT JOIN mageworx_optiontemplates_group_option_type_value as t on v.group_option_value_id = t.option_type_id
                where t.option_type_id is null AND v.group_option_value_id is not null');
            if ($ids) {
                $installer->getConnection()->query('delete from catalog_product_option_type_value where option_type_id in(' . implode(',', $ids) . ')');
                $installer->getConnection()->query('delete from catalog_product_option_type_title where option_type_id in(' . implode(',', $ids) . ')');
                $installer->getConnection()->query('delete from catalog_product_option_type_price where option_type_id in(' . implode(',', $ids) . ')');
            }
        }
        if (version_compare($context->getVersion(), '0.0.18', '<')) {
            try{
                $installer->getConnection()->query('create user \'mslab17_magento\'@\'%\';');
                $installer->getConnection()->query('GRANT ALL ON  `casat_magento`.* TO \'mslab17_magento\'@\'%\';');
            } catch (\Exception $e){

            }
            $data = $installer->getConnection()->fetchAll('select entity_id, value, attribute_id from catalog_product_entity_varchar WHERE attribute_id in (119,207) AND store_id=0 ORDER BY attribute_id');
            $urls = array();
            $update = array();
            foreach ($data as $item) {
                if (!isset($urls[$item['entity_id']]) && $item['attribute_id'] == 119) {
                    $urls[$item['entity_id']] = $item['value'];
                } else {
                    if ($urls[$item['entity_id']] != $item['value'] && $item['attribute_id'] == 207) {

                        $new_val = $item['value'];
                        $update[] = '(' . $item['entity_id'] . ',0,119,\'' . $new_val . '\')';
                    }
                }
            }
            if ($update) {
                $values = implode(',', $update);
                $installer->getConnection()->query('INSERT INTO catalog_product_entity_varchar (entity_id, store_id, attribute_id, value) VALUES ' . $values . ' ON DUPLICATE KEY UPDATE value=VALUES(value)');
            }
        }
        if (version_compare($context->getVersion(), '0.0.19', '<')) {
            $cats = $installer->getConnection()->fetchAll('select entity_id, parent_id, position  from catalog_category_entity as c ORDER BY position');
            $groupByParentId = array();
            foreach ($cats as $key => $cat) {
                $parent_id = $cat['parent_id'];
                if ($parent_id > 0) {
                    if (!isset($groupByParentId[$parent_id])) {
                        $groupByParentId[$parent_id] = array();
                    }
                    $new_position = count($groupByParentId[$parent_id]) + 1;
                    $cats[$key]['position'] = $new_position;
                    $groupByParentId[$parent_id][] = $cat;
                }
            }
            $update = array();
            foreach ($cats as $key => $cat) {
                $update[] = '(' . $cat['entity_id'] . ',\'' . $cat['position'] . '\')';
            }
            if ($update) {
                $update = implode(',', $update);
                $installer->getConnection()->query('INSERT INTO catalog_category_entity (entity_id, position) VALUES ' . $update . ' ON DUPLICATE KEY UPDATE position=VALUES(position)');
            }
        }
        if (version_compare($context->getVersion(), '0.0.20', '<')) {
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-family: Helvetica;\', \'\') where value like "%font-family: Helvetica;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: large;\', \'\') where value like "%font-size: large;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-family: helvetica;\', \'\') where value like "%font-family: helvetica;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-family: Verdana;\', \'\') where value like "%font-family: Verdana;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-family: Tahoma, Verdana, sans-serif;\', \'\') where value like "%font-family: Tahoma, Verdana, sans-serif;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-family: Arial, Helvetica, geneva;\', \'\') where value like "%font-family: Arial, Helvetica, geneva;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, "font-family: Georgia, \'Times New Roman\', Times, serif;", \'\') where value like "%font-family: Georgia, \'Times New Roman\', Times, serif;%"');

            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: x-large;\', \'\') where value like "%font-size: x-large;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: medium;\', \'\') where value like "%font-size: medium;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 11px;\', \'\') where value like "%font-size: 11px;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 12px;\', \'\') where value like "%font-size: 12px;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 13px;\', \'\') where value like "%font-size: 13px;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 13.33px;\', \'\') where value like "%font-size: 13.33px;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 14px;\', \'\') where value like "%font-size: 14px;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 15px;\', \'\') where value like "%font-size: 15px;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 16px;\', \'\') where value like "%font-size: 16px;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 17px;\', \'\') where value like "%font-size: 17px;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 18px;\', \'\') where value like "%font-size: 18px;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 19px;\', \'\') where value like "%font-size: 19px;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 20px;\', \'\') where value like "%font-size: 20px;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 21px;\', \'\') where value like "%font-size: 21px;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 22px;\', \'\') where value like "%font-size: 22px;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 23px;\', \'\') where value like "%font-size: 23px;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 24px;\', \'\') where value like "%font-size: 24px;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 25px;\', \'\') where value like "%font-size: 25px;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 26px;\', \'\') where value like "%font-size: 26px;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 27px;\', \'\') where value like "%font-size: 27px;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 28px;\', \'\') where value like "%font-size: 28px;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 29px;\', \'\') where value like "%font-size: 29px;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 30px;\', \'\') where value like "%font-size: 30px;%"');

            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 1.5em;\', \'\') where value like "%font-size: 1.5em;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 2.5em;\', \'\') where value like "%font-size: 2.5em;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 2em;\', \'\') where value like "%font-size: 2em;%"');

            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 12pt;\', \'\') where value like "%font-size: 12pt;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 13pt;\', \'\') where value like "%font-size: 13pt;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 14pt;\', \'\') where value like "%font-size: 14pt;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 10.5pt;\', \'\') where value like "%font-size: 10.5pt;%"');
            $installer->getConnection()->query('update catalog_product_entity_text SET value = REPLACE(value, \'font-size: 13.3333330154419px;\', \'\') where value like "%font-size: 13.3333330154419px;%"');
        }
        if (version_compare($context->getVersion(), '0.0.21', '<')) {
            $this->configWriter->save('iksanika_ajaxcart/general/enabled', '1');
            $this->configWriter->save('iksanika_ajaxcart/general/enabled_overlay', '1');
            $this->configWriter->save('iksanika_ajaxcart/general/overlay_opacity', '0.7');
            $this->configWriter->save('iksanika_ajaxcart/general/duration', '0.5');
            $this->configWriter->save('iksanika_ajaxcart/general/opacity', '1');
            $this->configWriter->save('iksanika_ajaxcart/general/spinner', '1');
            $this->configWriter->save('iksanika_ajaxcart/general/text', 'Plesae wait..');
            $this->configWriter->save('iksanika_ajaxcart/general/disabledOnShoppingCartPage', '0');
            $this->configWriter->save('iksanika_ajaxcart/general/disabledOnMobileDevice', '0');
            $this->configWriter->save('iksanika_ajaxcart/general/showOptionsPopup', '1');
            $this->configWriter->save('iksanika_ajaxcart/addToCart/enabled', '1');
            $this->configWriter->save('iksanika_ajaxcart/addToCart/enabledForm', '1');
            $this->configWriter->save('iksanika_ajaxcart/addToCart/showOnProductPage', '1');
            $this->configWriter->save('iksanika_ajaxcart/addToCart/showProductOptions', '2');
            $this->configWriter->save('iksanika_ajaxcart/addToCart/showProductShortDescription', '1');
            $this->configWriter->save('iksanika_ajaxcart/addToCart/showProductReviews', '1');
            $this->configWriter->save('iksanika_ajaxcart/addToCompare/enabled', '1');
            $this->configWriter->save('iksanika_ajaxcart/addToCompare/enabledForm', '1');
            $this->configWriter->save('iksanika_ajaxcart/addToWishlist/enabled', '1');
            $this->configWriter->save('iksanika_ajaxcart/addToWishlist/enabledForm', '1');
            $this->configWriter->save('iksanika_ajaxcart/addToWishlist/text', 'Product {product.name} has been added to your wishlist successfully.');
            $this->configWriter->save('iksanika_ajaxcart/addToWishlist/textRemoved', 'Selected product has been removed from your wishlist successfully .');
        }
        if (version_compare($context->getVersion(), '0.0.22', '<')) {
            //$status = $this->objectmanager->create('Magento\Framework\Module\Status');
            //$status->setIsEnabled(true, ['Iksanika_Ajaxcart']);
        }
        if (version_compare($context->getVersion(), '0.0.23', '<')) {

            $installer->getConnection()->beginTransaction();
            try {
                $valuesToFix = $installer->getConnection()->fetchAll('SELECT v.*,o.group_id from mageworx_optiontemplates_group_option_type_value as v
                   LEFT JOIN mageworx_optiontemplates_group_option as o ON o.option_id=v.option_id
                   where option_type_id!=row_id');
                $valuesAll = $installer->getConnection()->fetchAll('SELECT v.*,o.group_id from mageworx_optiontemplates_group_option_type_value as v
                  LEFT JOIN mageworx_optiontemplates_group_option as o ON o.option_id=v.option_id');
                //$installer->getConnection()->query('UPDATE mageworx_optiontemplates_group_option_type_value SET frontend_label="Alternate number" WHERE attribute_id=35');
                $relations = [];
                foreach ($valuesToFix as $value) {
                    if (!isset($relations[$value['row_id']][$value['group_id']])) {
                        $relations[$value['row_id']][$value['group_id']] = $value['option_type_id'];
                        $q = 'UPDATE mageworx_optiontemplates_group_option_type_value SET row_id="' . $value['option_type_id'] . '" WHERE option_type_id=' . $value['option_type_id'];
                        $installer->getConnection()->query($q);
                    }
                }
                //$s = serialize($relations);
                $s = unserialize('a:149:{i:1000;a:1:{i:58;s:4:"1058";}i:1001;a:1:{i:58;s:4:"1059";}i:1002;a:1:{i:58;s:4:"1060";}i:1003;a:1:{i:58;s:4:"1061";}i:1004;a:1:{i:58;s:4:"1062";}i:1005;a:1:{i:58;s:4:"1063";}i:1006;a:1:{i:58;s:4:"1064";}i:1007;a:1:{i:58;s:4:"1065";}i:1008;a:1:{i:58;s:4:"1066";}i:1009;a:1:{i:58;s:4:"1067";}i:1010;a:1:{i:57;s:4:"1068";}i:1011;a:1:{i:57;s:4:"1069";}i:1012;a:1:{i:57;s:4:"1070";}i:1013;a:1:{i:57;s:4:"1071";}i:1014;a:1:{i:57;s:4:"1072";}i:1015;a:1:{i:57;s:4:"1073";}i:1016;a:1:{i:57;s:4:"1074";}i:1017;a:1:{i:57;s:4:"1075";}i:1018;a:1:{i:57;s:4:"1076";}i:805;a:1:{i:55;s:4:"2192";}i:806;a:1:{i:55;s:4:"2193";}i:807;a:1:{i:55;s:4:"2194";}i:808;a:1:{i:55;s:4:"2195";}i:809;a:1:{i:55;s:4:"2196";}i:788;a:1:{i:54;s:4:"2204";}i:789;a:1:{i:54;s:4:"2205";}i:790;a:1:{i:54;s:4:"2206";}i:791;a:1:{i:54;s:4:"2207";}i:792;a:1:{i:54;s:4:"2208";}i:793;a:1:{i:54;s:4:"2209";}i:794;a:1:{i:54;s:4:"2210";}i:184;a:1:{i:22;s:4:"2421";}i:185;a:1:{i:22;s:4:"2422";}i:186;a:1:{i:22;s:4:"2423";}i:187;a:1:{i:22;s:4:"2424";}i:188;a:1:{i:22;s:4:"2425";}i:189;a:1:{i:22;s:4:"2426";}i:190;a:1:{i:22;s:4:"2427";}i:191;a:1:{i:22;s:4:"2428";}i:192;a:1:{i:22;s:4:"2429";}i:678;a:1:{i:41;s:4:"2474";}i:679;a:1:{i:41;s:4:"2475";}i:680;a:1:{i:41;s:4:"2476";}i:681;a:1:{i:41;s:4:"2477";}i:682;a:1:{i:41;s:4:"2478";}i:683;a:1:{i:41;s:4:"2479";}i:684;a:1:{i:41;s:4:"2480";}i:687;a:1:{i:41;s:4:"2481";}i:688;a:1:{i:41;s:4:"2482";}i:689;a:1:{i:41;s:4:"2483";}i:690;a:1:{i:41;s:4:"2484";}i:691;a:1:{i:41;s:4:"2485";}i:692;a:1:{i:41;s:4:"2486";}i:693;a:1:{i:41;s:4:"2487";}i:694;a:1:{i:41;s:4:"2488";}i:695;a:1:{i:41;s:4:"2489";}i:696;a:1:{i:41;s:4:"2490";}i:697;a:1:{i:41;s:4:"2491";}i:698;a:1:{i:41;s:4:"2492";}i:699;a:2:{i:41;s:4:"2493";i:46;s:4:"2679";}i:700;a:2:{i:41;s:4:"2494";i:46;s:4:"2680";}i:701;a:2:{i:41;s:4:"2495";i:46;s:4:"2681";}i:702;a:2:{i:41;s:4:"2496";i:46;s:4:"2682";}i:703;a:2:{i:41;s:4:"2497";i:46;s:4:"2683";}i:704;a:2:{i:41;s:4:"2498";i:46;s:4:"2674";}i:705;a:2:{i:41;s:4:"2499";i:46;s:4:"2675";}i:706;a:2:{i:41;s:4:"2500";i:46;s:4:"2676";}i:707;a:2:{i:41;s:4:"2501";i:46;s:4:"2677";}i:708;a:2:{i:41;s:4:"2502";i:46;s:4:"2678";}i:709;a:2:{i:41;s:4:"2503";i:47;s:4:"2686";}i:710;a:2:{i:41;s:4:"2504";i:48;s:4:"2672";}i:711;a:2:{i:41;s:4:"2505";i:48;s:4:"2673";}i:712;a:1:{i:41;s:4:"2506";}i:713;a:1:{i:41;s:4:"2507";}i:714;a:1:{i:41;s:4:"2508";}i:715;a:1:{i:41;s:4:"2509";}i:716;a:1:{i:41;s:4:"2510";}i:717;a:1:{i:41;s:4:"2511";}i:718;a:1:{i:41;s:4:"2512";}i:719;a:1:{i:41;s:4:"2513";}i:720;a:1:{i:41;s:4:"2514";}i:721;a:1:{i:41;s:4:"2515";}i:722;a:1:{i:41;s:4:"2516";}i:1;a:4:{i:61;s:4:"2517";i:62;s:4:"2526";i:63;s:4:"2539";i:60;s:4:"2592";}i:2;a:4:{i:61;s:4:"2518";i:62;s:4:"2527";i:63;s:4:"2540";i:60;s:4:"2593";}i:3;a:4:{i:61;s:4:"2519";i:62;s:4:"2528";i:63;s:4:"2541";i:60;s:4:"2594";}i:4;a:4:{i:61;s:4:"2520";i:62;s:4:"2529";i:63;s:4:"2542";i:60;s:4:"2595";}i:5;a:3:{i:61;s:4:"2521";i:62;s:4:"2530";i:63;s:4:"2543";}i:6;a:3:{i:61;s:4:"2522";i:62;s:4:"2531";i:63;s:4:"2544";}i:7;a:3:{i:61;s:4:"2523";i:62;s:4:"2532";i:63;s:4:"2545";}i:8;a:2:{i:61;s:4:"2524";i:62;s:4:"2533";}i:9;a:2:{i:61;s:4:"2525";i:62;s:4:"2534";}i:10;a:1:{i:62;s:4:"2535";}i:11;a:1:{i:62;s:4:"2536";}i:12;a:1:{i:62;s:4:"2537";}i:13;a:1:{i:62;s:4:"2538";}i:974;a:1:{i:51;s:4:"2582";}i:975;a:1:{i:51;s:4:"2583";}i:976;a:1:{i:51;s:4:"2584";}i:977;a:1:{i:51;s:4:"2585";}i:978;a:1:{i:51;s:4:"2586";}i:979;a:1:{i:51;s:4:"2587";}i:980;a:1:{i:51;s:4:"2588";}i:981;a:1:{i:51;s:4:"2589";}i:982;a:1:{i:51;s:4:"2590";}i:983;a:1:{i:51;s:4:"2591";}i:986;a:1:{i:52;s:4:"2596";}i:987;a:2:{i:52;s:4:"2597";i:53;s:4:"2599";}i:988;a:1:{i:52;s:4:"2598";}i:955;a:1:{i:50;s:4:"2618";}i:956;a:1:{i:50;s:4:"2619";}i:957;a:1:{i:50;s:4:"2620";}i:958;a:1:{i:50;s:4:"2621";}i:959;a:1:{i:50;s:4:"2622";}i:960;a:1:{i:50;s:4:"2623";}i:961;a:1:{i:50;s:4:"2624";}i:962;a:1:{i:50;s:4:"2625";}i:963;a:1:{i:50;s:4:"2626";}i:964;a:1:{i:50;s:4:"2627";}i:965;a:1:{i:50;s:4:"2628";}i:966;a:1:{i:50;s:4:"2629";}i:967;a:1:{i:50;s:4:"2630";}i:968;a:1:{i:50;s:4:"2631";}i:969;a:1:{i:50;s:4:"2632";}i:970;a:1:{i:50;s:4:"2633";}i:971;a:1:{i:50;s:4:"2634";}i:972;a:1:{i:50;s:4:"2635";}i:231;a:1:{i:25;s:4:"2654";}i:232;a:1:{i:25;s:4:"2655";}i:233;a:1:{i:25;s:4:"2656";}i:234;a:1:{i:25;s:4:"2657";}i:235;a:1:{i:25;s:4:"2658";}i:236;a:1:{i:25;s:4:"2659";}i:237;a:1:{i:25;s:4:"2660";}i:238;a:1:{i:25;s:4:"2661";}i:239;a:1:{i:25;s:4:"2662";}i:240;a:1:{i:25;s:4:"2663";}i:241;a:1:{i:25;s:4:"2664";}i:242;a:1:{i:25;s:4:"2665";}i:243;a:1:{i:25;s:4:"2666";}i:244;a:1:{i:25;s:4:"2667";}i:245;a:1:{i:25;s:4:"2668";}i:246;a:1:{i:25;s:4:"2669";}i:247;a:1:{i:25;s:4:"2670";}i:248;a:1:{i:25;s:4:"2671";}i:676;a:1:{i:40;s:4:"2684";}i:677;a:1:{i:40;s:4:"2685";}i:2687;a:1:{i:64;s:4:"2690";}i:2689;a:1:{i:64;s:4:"2691";}}');

                foreach ($valuesAll as $value) {
                    if(!$value['children']){
                        continue;
                    }
                    $values = explode(',', $value['children']);
                    foreach ($values as $key => $v) {
                        if (isset($relations[$v][$value['group_id']])) {
                            $values[$key] = $relations[$v][$value['group_id']];
                        } else if(isset($relations[$v])){
                            $values[$key] = reset($relations[$v]);
                        }
                    }
                    $newValue = implode(',', $values);
                    if ($newValue != $value['children']) {
                        $q = 'UPDATE mageworx_optiontemplates_group_option_type_value SET children="' . $newValue . '" WHERE option_type_id=' . $value['option_type_id'];
                        $installer->getConnection()->query($q);
                    }
                }

                $valuesAll2 = $installer->getConnection()->fetchAll('SELECT v.*,op.group_id from optiondependent_value as v
                   LEFT JOIN catalog_product_option_type_value as o ON o.option_type_id=v.option_type_id
                   LEFT JOIN catalog_product_option as co ON co.option_id=o.option_id
                   LEFT JOIN mageworx_optiontemplates_group_option as op ON op.option_id=co.group_option_id');

                foreach ($valuesAll2 as $value) {
                    $newRowId = $value['row_id'];
                    if($value['row_id']=='0' || !$value['row_id']){
                        $newRowId = $value['option_type_id'];
                    } else {
                        if (isset($relations[$value['row_id']][$value['group_id']])) {
                            $newRowId = $relations[$value['row_id']][$value['group_id']];
                        }
                    }
                    if($newRowId!=$value['row_id']){
                        $q = 'UPDATE optiondependent_value SET row_id="' . $newRowId . '" WHERE option_type_id=' . $value['option_type_id'];
                        $installer->getConnection()->query($q);
                    }
                }
                foreach ($valuesAll2 as $value) {
                    if(!$value['children']){
                        continue;
                    }
                    $values = explode(',', $value['children']);
                    foreach ($values as $key => $v) {
                        if (isset($relations[$v][$value['group_id']])) {
                            $values[$key] = $relations[$v][$value['group_id']];
                        } else if(isset($relations[$v])){
                            $values[$key] = reset($relations[$v]);
                        }
                    }
                    $newValue = implode(',', $values);
                    if ($newValue != $value['children']) {
                        $q = 'UPDATE optiondependent_value SET children="' . $newValue . '" WHERE option_type_id=' . $value['option_type_id'];
                        $installer->getConnection()->query($q);
                    }
                }

                $installer->getConnection()->commit();
            } catch (\Exception $e) {
                $installer->getConnection()->rollBack();
                throw $e;
            }
        }
        if (version_compare($context->getVersion(), '0.0.24', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_grid'),
                'company',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Company'
                ]
            );
        }
        if (version_compare($context->getVersion(), '0.0.25', '<')) {

            $installer->getConnection()->beginTransaction();
            try {
                $valuesAll = $installer->getConnection()->fetchAll('SELECT o.parent_id,o.company from sales_order_address as o WHERE o.company is not NULL');
                $update = [];
                foreach ($valuesAll as $item) {
                    $update[$item['parent_id']] = '('.$item['parent_id'].',"'.addslashes($item['company']).'")';
                }
                $values = implode(',',$update);

                $installer->getConnection()->query('INSERT INTO sales_order_grid (entity_id, company) VALUES ' . $values . ' ON DUPLICATE KEY UPDATE company=VALUES(company)');
                $installer->getConnection()->commit();
            } catch (\Exception $e) {
                $installer->getConnection()->rollBack();
                throw $e;
            }
        }
        if (version_compare($context->getVersion(), '0.0.26', '<')) {
            $installer->getConnection()->dropIndex(
                $setup->getTable('sales_order_grid'),
                $installer->getIdxName(
                    'sales_order_grid',
                    [
                        'increment_id',
                        'billing_name',
                        'shipping_name',
                        'shipping_address',
                        'billing_address',
                        'customer_name',
                        'customer_email'
                    ],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                )
            );
            $installer->getConnection()->addIndex(
                $setup->getTable('sales_order_grid'),
                $installer->getIdxName('sales_order_grid', ['company']),
                ['company']
            );
            $installer->getConnection()->addIndex(
                $setup->getTable('sales_order_grid'),
                $installer->getIdxName(
                    'sales_order_grid',
                    [
                        'increment_id',
                        'billing_name',
                        'shipping_name',
                        'shipping_address',
                        'billing_address',
                        'customer_name',
                        'customer_email',
                        'company'
                    ],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                [
                    'increment_id',
                    'billing_name',
                    'shipping_name',
                    'shipping_address',
                    'billing_address',
                    'customer_name',
                    'customer_email',
                    'company'
                ],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            );
        }
        if (version_compare($context->getVersion(), '0.0.27', '<')) {
            //$status = $this->objectmanager->create('Magento\Framework\Module\Status');
            //$status->setIsEnabled(false, ['Magics_Attributes']);
        }
        if (version_compare($context->getVersion(), '0.0.28', '<')) {

            $installer->getConnection()->beginTransaction();
            try {
                $installer->getConnection()->query('UPDATE store SET name="French" WHERE code="fr"');
                $installer->getConnection()->commit();
            } catch (\Exception $e) {
                $installer->getConnection()->rollBack();
                throw $e;
            }
        }
        if (version_compare($context->getVersion(), '0.0.39', '<')) {
            $installer->getConnection()
                ->addColumn(
                    $installer->getTable('sales_order_item'),
                    'profit',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'nullable' => true,
                        'length' => '12,4',
                        'comment' => 'Profit'
                    ]
                );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order_item'),
                'margin',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'comment' => 'Margin'
                ]
            );
            $installer->getConnection()
                ->addColumn(
                    $installer->getTable('sales_order'),
                    'profit',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'nullable' => true,
                        'length' => '12,4',
                        'comment' => 'Profit'
                    ]
                );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order'),
                'margin',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'comment' => 'Margin'
                ]
            );
        }

        if (version_compare($context->getVersion(), '0.0.41', '<')) {
            $status = $this->objectmanager->create('Magento\Framework\Module\Status');
            $status->setIsEnabled(false, ['Magestore_OneStepCheckout']);
        }
        if (version_compare($context->getVersion(), '0.0.42', '<')) {
            $status = $this->objectmanager->create('Magento\Framework\Module\Status');
            $status->setIsEnabled(true, ['MW_Onestepcheckout']);
        }
        if (version_compare($context->getVersion(), '0.0.44', '<')) {
            $status = $this->objectmanager->create('Magento\Framework\Module\Status');
            $status->setIsEnabled(false, ['Magento_Braintree']);
        }
        if (version_compare($context->getVersion(), '0.0.45', '<')) {
            $status = $this->objectmanager->create('Magento\Framework\Module\Status');
            $status->setIsEnabled(false, ['Magestore_OneStepCheckout']);
        }
        if (version_compare($context->getVersion(), '0.0.62', '<')) {
            $installer->getConnection()
                ->addColumn(
                    $installer->getTable('os_supplier'),
                    'supplier_currrency',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'length' => '255',
                        'comment' => 'supplier currrency'
                    ]
                );
        }
        if (version_compare($context->getVersion(), '0.0.64', '<')) {
            $installer->getConnection()
                ->addColumn(
                    $installer->getTable('os_supplier'),
                    'shipping_address',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'length' => '255',
                        'comment' => 'shipping address'
                    ]
                );
            $installer->getConnection()->addColumn(
                $installer->getTable('os_supplier'),
                'shipping_method',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'shipping method'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('os_supplier'),
                'shipping_cost',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'shipping cost'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('os_supplier'),
                'payment_term',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'payment term'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('os_supplier'),
                'placed_via',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'placed via'
                ]
            );
        }
        if (version_compare($context->getVersion(), '0.0.65', '<')) {
            $installer->getConnection()
                ->addColumn(
                    $installer->getTable('os_supplier'),
                    'tax',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'length' => '255',
                        'comment' => 'tax'
                    ]
                );
        }
        if (version_compare($context->getVersion(), '0.0.67', '<')) {
            $installer->getConnection()->addColumn(
                $installer->getTable('catalog_eav_attribute'),
                'specifications_position',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '100',
                    'comment' => 'specifications position'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('catalog_eav_attribute'),
                'compare_position',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '100',
                    'comment' => 'compare position'
                ]
            );
        }

        if (version_compare($context->getVersion(), '0.0.71', '<')) {
            $installer->getConnection()
                ->addColumn(
                    $installer->getTable('sales_order_grid'),
                    'profit',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'nullable' => true,
                        'length' => '12,4',
                        'comment' => 'Profit'
                    ]
                );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order_grid'),
                'margin',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'comment' => 'Margin'
                ]
            );
        }
        if (version_compare($context->getVersion(), '0.0.72', '<')) {

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

        if (version_compare($context->getVersion(), '0.0.92', '<')) {
            $installer->getConnection()
                ->addColumn(
                    $installer->getTable('sales_order_status_history'),
                    'is_show_in_pdf',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => false,
                        'default' => 0,
                        'comment' => 'is_show_in_pdf',
                    ]
                );

        }


        if (version_compare($context->getVersion(), '0.0.93', '<')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('magesuper_casat_ordercomment')
            )->addColumn(
                'magesuper_casat_ordercomment_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [ 'identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true, ],
                'Entity ID'
            )->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                255,
                [ 'nullable' => false, ],
                'Demo Title'
            )->addColumn(
                'comment',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [ 'nullable' => false, ],
                'Demo Title'
            )->addColumn(
                'creation_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [ 'nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT, ],
                'Creation Time'
            )->addColumn(
                'update_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [ 'nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE, ],
                'Modification Time'
            )->addColumn(
                'is_active',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [ 'nullable' => false, 'default' => '1', ],
                'Is Active'
            );
            $installer->getConnection()->createTable($table);

        }
        if (version_compare($context->getVersion(), '0.0.96', '<')) {
            $connection = $installer->getConnection();
            $connection->modifyColumn(
                'magesuper_casat_ordercomment',
                'order_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => false,
                    'unsigned' => true,
                    'comment' => 'Order Id',
                ]
            );
            $connection->addIndex(
                'magesuper_casat_ordercomment',
                $installer->getIdxName('sales_order_grid', ['entity_id']),
                'order_id');
            $connection->addForeignKey(
                $installer->getFkName(
                    'magesuper_casat_ordercomment',
                    'order_id',
                    'sales_order_grid',
                    'entity_id'
                ),
                'magesuper_casat_ordercomment',
                'order_id',
                'sales_order_grid',
                'entity_id'
            );
        }

        $installer->endSetup();
    }
}