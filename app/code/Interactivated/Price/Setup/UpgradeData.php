<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Interactivated\Price\Setup;

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
    function __construct(CategorySetupFactory $categorySetupFactory,
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
    function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();


        if (version_compare($context->getVersion(), '0.0.1') < 0) {
            // set new resource model paths
            /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $categorySetup->addAttribute(
                ProductAttributeInterface::ENTITY_TYPE_CODE,
                'price_usd',
                [
                    'type' => 'decimal',
                    'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Price',
                    'frontend' => '',
                    'label' => 'Price USD',
                    'input' => 'price',
                    'class' => '',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'apply_to' => '',
                    'visible_on_front' => false,
                    'used_in_product_listing' => true,
                    'sort_order' => 200,
                    'group' => 'Advanced Pricing',
                ]
            );
        }
        if (version_compare($context->getVersion(), '0.0.2') < 0) {
            // set new resource model paths
            /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $categorySetup->addAttribute(
                ProductAttributeInterface::ENTITY_TYPE_CODE,
                'special_price_usd',
                [
                    'type' => 'decimal',
                    'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Price',
                    'frontend' => '',
                    'label' => 'Special Price USD',
                    'input' => 'price',
                    'class' => '',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'apply_to' => '',
                    'visible_on_front' => false,
                    'used_in_product_listing' => true,
                    'sort_order' => 205,
                    'group' => 'Advanced Pricing',
                ]
            );
            $categorySetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'usd_is_base_price',
                [
                    'type' => 'int',
                    'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Boolean',
                    'frontend' => '',
                    'label' => 'USD Price is a base',
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
                    'sort_order' => 210,
                    'used_in_product_listing' => true,
                    'group' => 'Advanced Pricing',
                ]
            );
        }
        $setup->endSetup();
    }

}
