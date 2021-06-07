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

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Model\ResourceModel\Entity\Attribute as EntityAttribute;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var eavSetupFactory
     */
    private $eavSetupFactory = null;

    /**
     * @var product
     */
    private $product = null;
    
    /**
     * @var eavSetup
     */
    private $eavSetup = null;
    
    /**
     * @var Context
     */
    private $version = null;
    
    /**
     * @var EntityAttribute
     */
    private $eavAttribute;

    /**
     * @param EavSetupFactory $eavSetupFactory
     * @param Product $product
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        Product $product,
        EntityAttribute $eavAttribute,
        Installer $installer
    ) {
        $this->product = $product;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavAttribute = $eavAttribute;
        $this->installer = $installer;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        
        $this->eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $this->version = $context->getVersion();

        $this->addProductAttribute();
        $this->showAttributeOnProductListing();
        
        if ($context->getVersion()) {
            if (version_compare($context->getVersion(), '200.0.0') < 0) {
                $this->upgradeProductTrialAttribute($setup);
            }
            
            if (version_compare($context->getVersion(), '200.0.2', '<')) {
                $this->installer->upgradeData($setup);
            }
        }

        $setup->endSetup();
        
        $this->eavSetup = null;
    }
    
    /**
     * Add Product Attribute
     */
    private function addProductAttribute()
    {
        $this->eavSetup->addAttributeGroup(
            Product::ENTITY,
            'Default',
            'Subscribe Now',
            '26'
        );

        $this->eavSetup->addAttribute(
            Product::ENTITY,
            'is_subscription',
            [
                'type' => 'int',
                'label' => 'Enable Subscribe Now',
                'input' => 'select',
                'required' => false,
                'global' => Attribute::SCOPE_GLOBAL,
                'group' => 'Subscribe Now',
                'used_in_product_listing' => true,
                'visible_on_front' => false,
                'is_used_for_promo_rules' => true, // Magedelight 2352016 to apply discount for subscription product
                'source' => 'Magedelight\Subscribenow\Model\Source\SusbscriptionOption',
                'default' => 0
            ]
        );

        $this->eavSetup->addAttribute(
            Product::ENTITY,
            'subscription_type',
            [
                'type' => 'varchar',
                'label' => 'Product Purchase Option',
                'input' => 'select',
                'required' => false,
                'global' => Attribute::SCOPE_GLOBAL,
                'group' => 'Subscribe Now',
                'used_in_product_listing' => true,
                'visible_on_front' => false,
                'is_used_for_promo_rules' => true,
                'source' => 'Magedelight\Subscribenow\Model\Source\PurchaseOption',
                'sort' => 20
            ]
        );

        $this->eavSetup->addAttribute(
            Product::ENTITY,
            'discount_type',
            [
                'type' => 'varchar',
                'label' => 'Discount Type',
                'input' => 'select',
                'required' => false,
                'global' => Attribute::SCOPE_GLOBAL,
                'group' => 'Subscribe Now',
                'used_in_product_listing' => true,
                'visible_on_front' => false,
                'is_used_for_promo_rules' => true,
                'source' => 'Magedelight\Subscribenow\Model\Source\DiscountType',
                'sort' => 30
            ]
        );

        $this->eavSetup->addAttribute(
            Product::ENTITY,
            'discount_amount',
            [
                'type' => 'decimal',
                'label' => 'Discount On Subscription',
                'input' => 'price',
                'required' => false,
                'global' => Attribute::SCOPE_GLOBAL,
                'group' => 'Subscribe Now',
                'used_in_product_listing' => true,
                'visible_on_front' => false,
                'class' => 'validate-greater-than-zero',
                'comment' => 'Discount will applied on product price.',
                'sort' => 40
            ]
        );

        $this->eavSetup->addAttribute(
            Product::ENTITY,
            'initial_amount',
            [
                'type' => 'decimal',
                'label' => 'Initial Fee',
                'input' => 'price',
                'required' => false,
                'global' => Attribute::SCOPE_GLOBAL,
                'group' => 'Subscribe Now',
                'used_in_product_listing' => true,
                'visible_on_front' => false,
                'class' => 'validate-number validate-greater-than-zero',
                'sort' => 50
            ]
        );

        $this->eavSetup->addAttribute(
            Product::ENTITY,
            'billing_period_type',
            [
                'type' => 'varchar',
                'label' => 'Billing Period Defined By',
                'input' => 'select',
                'required' => false,
                'global' => Attribute::SCOPE_GLOBAL,
                'group' => 'Subscribe Now',
                'used_in_product_listing' => true,
                'visible_on_front' => false,
                'is_used_for_promo_rules' => true,
                'source' => 'Magedelight\Subscribenow\Model\Source\BillingPeriodBy',
                'sort' => 60
            ]
        );

        $this->eavSetup->addAttribute(
            Product::ENTITY,
            'billing_period',
            [
                'type' => 'varchar',
                'label' => 'Billing Period',
                'input' => 'select',
                'required' => false,
                'global' => Attribute::SCOPE_GLOBAL,
                'group' => 'Subscribe Now',
                'used_in_product_listing' => true,
                'visible_on_front' => false,
                'is_used_for_promo_rules' => true,
                'source' => 'Magedelight\Subscribenow\Model\Source\SubscriptionInterval',
                'sort' => 70
            ]
        );

        $this->eavSetup->addAttribute(
            Product::ENTITY,
            'billing_max_cycles',
            [
                'type' => 'text',
                'label' => 'Number Of Billing Cycle',
                'backend' => '\Magedelight\Subscribenow\Model\Attribute\Backend\NumberOfBillingCycle',
                'input' => 'text',
                'required' => false,
                'global' => Attribute::SCOPE_GLOBAL,
                'group' => 'Subscribe Now',
                'used_in_product_listing' => true,
                'visible_on_front' => false,
                'class' => 'validate-number validate-digits validate-greater-than-zero',
                'sort' => 80
            ]
        );

        $this->eavSetup->addAttribute(
            Product::ENTITY,
            'define_start_from',
            [
                'type' => 'varchar',
                'label' => 'Subscription Start From',
                'input' => 'select',
                'required' => false,
                'global' => Attribute::SCOPE_GLOBAL,
                'group' => 'Subscribe Now',
                'used_in_product_listing' => true,
                'visible_on_front' => false,
                'is_used_for_promo_rules' => true,
                'source' => 'Magedelight\Subscribenow\Model\Source\SubscriptionStart',
                'sort' => 90
            ]
        );

        $this->eavSetup->addAttribute(
            Product::ENTITY,
            'day_of_month',
            [
                'type' => 'text',
                'backend' => '\Magedelight\Subscribenow\Model\Attribute\Backend\Dayofmonth',
                'label' => 'Day Of Month',
                'input' => 'text',
                'required' => false,
                'global' => Attribute::SCOPE_GLOBAL,
                'group' => 'Subscribe Now',
                'used_in_product_listing' => true,
                'visible_on_front' => false,
                'class' => 'validate-greater-than-zero validate-digits-range digits-range-1-31',
                'sort' => 100
            ]
        );

        $this->eavSetup->addAttribute(
            Product::ENTITY,
            'allow_update_date',
            [
                'type' => 'int',
                'label' => 'Allow Subscribers To Update Next Subscription Date',
                'input' => 'select',
                'required' => false,
                'global' => Attribute::SCOPE_GLOBAL,
                'group' => 'Subscribe Now',
                'used_in_product_listing' => false,
                'visible_on_front' => false,
                'is_used_for_promo_rules' => false,
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'sort' => 110
            ]
        );

        $this->eavSetup->addAttribute(
            Product::ENTITY,
            'allow_trial',
            [
                'type' => 'int',
                'label' => 'Trial Billing Period',
                'input' => 'select',
                'required' => false,
                'global' => Attribute::SCOPE_GLOBAL,
                'group' => 'Subscribe Now',
                'used_in_product_listing' => true,
                'visible_on_front' => false,
                'is_used_for_promo_rules' => false,
                'source' => 'Magedelight\Subscribenow\Model\Source\TrialOption',
                'sort' => 120,
                'default' => 0
            ]
        );

        $this->eavSetup->addAttribute(
            Product::ENTITY,
            'trial_period',
            [
                'type' => 'varchar',
                'label' => 'Trial Period',
                'input' => 'select',
                'required' => false,
                'global' => Attribute::SCOPE_GLOBAL,
                'group' => 'Subscribe Now',
                'used_in_product_listing' => false,
                'visible_on_front' => false,
                'is_used_for_promo_rules' => true,
                'source' => 'Magedelight\Subscribenow\Model\Source\SubscriptionInterval',
                'sort' => 130
            ]
        );

        $this->eavSetup->addAttribute(
            Product::ENTITY,
            'trial_amount',
            [
                'type' => 'text',
                'backend' => '\Magedelight\Subscribenow\Model\Attribute\Backend\TrialBillingAmount',
                'label' => 'Trial Billing Amount',
                'input' => 'text',
                'required' => false,
                'global' => Attribute::SCOPE_GLOBAL,
                'group' => 'Subscribe Now',
                'used_in_product_listing' => false,
                'visible_on_front' => false,
                'class' => 'validate-number validate-zero-or-greater',
                'sort' => 140
            ]
        );

        $this->eavSetup->addAttribute(
            Product::ENTITY,
            'trial_maxcycle',
            [
                'type' => 'text',
                'backend' => '\Magedelight\Subscribenow\Model\Attribute\Backend\NumberOfTrialCycle',
                'label' => 'Number Of Trial Cycle',
                'input' => 'text',
                'required' => false,
                'global' => Attribute::SCOPE_GLOBAL,
                'group' => 'Subscribe Now',
                'used_in_product_listing' => false,
                'visible_on_front' => false,
                'class' => 'validate-greater-than-zero',
                'sort' => 150
            ]
        );
    }
    
    /**
     * Update Attribute Value
     * `used_in_product_listing` = true
     */
    private function showAttributeOnProductListing()
    {
        if (version_compare($this->version, '100.1.3', '<')) {
            $attributeIds = [
                'subscription_type',
                'billing_period_type',
                'billing_period',
                'define_start_from',
                'allow_trial'
            ];

            foreach ($attributeIds as $attributeId) {
                $this->eavSetup->updateAttribute(Product::ENTITY, $attributeId, 'used_in_product_listing', true);
            }
        }
    }
    
    private function upgradeProductTrialAttribute($setup)
    {
        $attribute_id = $this->eavAttribute->getIdByCode('catalog_product', 'allow_trial');
        $table = $setup->getTable('catalog_product_entity_int');
        $setup->getConnection()->query("UPDATE `$table` SET `value` = IF (`value`, 0, 1) WHERE `attribute_id` = '$attribute_id'");
    }
}
