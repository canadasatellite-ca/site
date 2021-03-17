<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Quote\Setup\QuoteSetup;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Sales\Setup\SalesSetup;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Setup\EavSetup;

class UpgradeData implements UpgradeDataInterface
{

    /**
     * Quote setup factory
     *
     * @var QuoteSetupFactory
     */
    private $quoteSetupFactory;

    /**
     * Customer setup factory
     *
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * Sales setup factory
     *
     * @var SalesSetupFactory
     */
    private $salesSetupFactory;

    /**
     * EAV setup factory
     *
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param QuoteSetupFactory $setupFactory
     */
    public function __construct(
        QuoteSetupFactory $setupFactory,
        CustomerSetupFactory $customerSetupFactory,
        SalesSetupFactory $salesSetupFactory,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->quoteSetupFactory = $setupFactory;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->addAttributeToQuoteAddress($setup);
            $this->addAttributeToCustomerAddress($setup);
            $this->addAttributeToOrderAddress($setup);
        }

        if (version_compare($context->getVersion(), '1.1.1', '<')) {
            $setup->getConnection()
                ->dropColumn(
                    $setup->getTable('quote_address'),
                    'canada_dpo_postcode'
                );

            /** @var CustomerSetup $customerSetup */
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            $customerSetup->updateAttribute('customer_address', 'canada_dpo_id', 'backend_type', 'varchar');
        }

        if (version_compare($context->getVersion(), '1.4.1', '<')) {
            $this->addNonMailableProductAttribute($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param $setup
     */
    private function addAttributeToQuoteAddress($setup)
    {
        /** @var QuoteSetup $quoteSetup */
        $quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);
        $attributes = [
            'canada_dpo_id'        => ['type' => Table::TYPE_TEXT],
            'canada_dpo_postcode'  => ['type' => Table::TYPE_TEXT],
        ];

        foreach ($attributes as $attributeCode => $attributeParams) {
            $quoteSetup->addAttribute('quote_address', $attributeCode, $attributeParams);
        }
    }

    /**
     * @param $setup
     */
    private function addAttributeToCustomerAddress($setup)
    {
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $attributes = [
            'canada_dpo_id' => [
                'label'     => 'CanadaPost Office Id',
                'type'      => 'static',
                'input'     => 'text',
                'position'  => 200,
                'visible'   => true,
                'required'  => false,
            ],
        ];

        foreach ($attributes as $attributeCode => $attributeParams) {
            $customerSetup->addAttribute('customer_address', $attributeCode, $attributeParams);
            $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', $attributeCode);
            $attribute->setData(
                'used_in_forms',
                ['adminhtml_customer_address', 'customer_address_edit']
            );
            $attribute->save();
        }
    }

    /**
     * @param $setup
     */
    private function addAttributeToOrderAddress($setup)
    {
        /** @var SalesSetup $salesSetup */
        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
        $attributes = [
            'canada_dpo_id'        => ['type' => Table::TYPE_TEXT],
        ];

        foreach ($attributes as $attributeCode => $attributeParams) {
            $salesSetup->addAttribute('order_address', $attributeCode, $attributeParams);
        }
    }

    /**
     * @param $setup
     */
    private function addNonMailableProductAttribute($setup)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'non_mailable_product',
            [
                'type' => 'int',
                'label' => 'Non Mailable',
                'input' => 'boolean',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'required' => false,
                'sort_order' => 1050,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'Product Details',
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'backend' => '',
                'frontend' => '',
                'class' => '',
                'visible' => true,
                'user_defined' => false,
                'default' => '0',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => ''
            ]
        );
    }
}
