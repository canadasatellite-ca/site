<?php

/**
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

namespace Magestore\Webpos\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Setup\EavSetup;

/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeData implements UpgradeDataInterface
{
    
    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;
    
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;
    
    /**
    * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
    */
    protected $_eavAttribute;

    /**
     * @var Magento\Framework\App\ProductMetadata
     */
    protected $productMetadata;

    /**
     * UpgradeData constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ){
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->_eavAttribute = $eavAttribute;
        $this->productMetadata = $productMetadata;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $attributeId = $this->_eavAttribute->getIdByCode('catalog_product', 'webpos_visible');
            $action = \Magento\Framework\App\ObjectManager::getInstance()->create(
           '\Magento\Catalog\Model\ResourceModel\Product\Action'
            );
            $connection = $action->getConnection();
            $table = $setup->getTable('catalog_product_entity_int');
            //set invisible for default
            $productCollection = \Magento\Framework\App\ObjectManager::getInstance()->create(
                '\Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection'
            );
            $visibleInSite = \Magento\Framework\App\ObjectManager::getInstance()->create(
                '\Magento\Catalog\Model\Product\Visibility'
            )->getVisibleInSiteIds();

            $productCollection->addAttributeToFilter('visibility', ['nin' => $visibleInSite]);

            $version = $this->productMetadata->getVersion();
            $edition = $this->productMetadata->getEdition();
            foreach($productCollection->getAllIds() as $productId){
                if($edition == 'Enterprise' && version_compare($version, '2.1.5', '>=')){
                    $data = [
                        'attribute_id'  => $attributeId,
                        'store_id'  => 0,
                        'row_id' => $productId,
                        'value' => 0
                    ];
                }else{
                    $data = [
                        'attribute_id'  => $attributeId,
                        'store_id'  => 0,
                        'entity_id' => $productId,
                        'value' => 0
                    ];
                }
                $connection->insertOnDuplicate($table, $data, ['value']);
            }
        }
        $setup->endSetup();
    }
}
