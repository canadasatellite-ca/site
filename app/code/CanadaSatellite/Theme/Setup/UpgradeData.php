<?php
namespace CanadaSatellite\Theme\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Quote\Setup\QuoteSetup;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetup;
use Magento\Sales\Setup\SalesSetupFactory;


class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * Quote setup factory
     *
     * @var QuoteSetupFactory
     */
    private $quoteSetupFactory;

    /**
     * Sales setup factory
     *
     * @var SalesSetupFactory
     */
    private $salesSetupFactory;

    /**
     * UpgradeData constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param CustomerSetupFactory $customerSetupFactory
     */
    function __construct(
        EavSetupFactory $eavSetupFactory,
        CustomerSetupFactory $customerSetupFactory,
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory
    ){
        $this->eavSetupFactory = $eavSetupFactory;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        if (version_compare($context->getVersion(), "1.0.3", "<")) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            /** @var EavSetup $eavSetup*/
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'layered_navigation',
                [
                    'type' => 'int',
                    'label' => 'layered_navigation',
                    'input' => 'boolean',
                    'sort_order' => 85,
                    'source' => '',
                    'global' => 1,
                    'visible' => true,
                    'required' => true,
                    'user_defined' => false,
                    'default' => null,
                    'group' => 'General Information',
                    'backend' => ''
                ]
            );
        }

        if (version_compare($context->getVersion(), "1.0.4", "<")) {
            /** @var CustomerSetup $customerSetup */
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

            $customerSetup->addAttribute('customer_address', 'extension_phone', [
                'type' => 'varchar',
                'label' => 'Extension',
                'input' => 'text',
                'required' => false,
                'visible' => true,
                'sort_order' => 1000,
                'position' => 1000
            ]);

            $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'extension_phone')
                ->setData('used_in_forms',
                    ['adminhtml_checkout', 'adminhtml_customer', 'adminhtml_customer_address',
                     'customer_account_edit', 'customer_address_edit', 'customer_register_address']
                );

            $attribute->save();
        }

        if (version_compare($context->getVersion(), "1.0.5", "<")) {
            $this->_addAttributeToQuoteAddress($setup);
            $this->_addAttributeToOrderAddress($setup);

            $setup->getConnection()->addColumn(
                $setup->getTable('customer_address_entity'),
                'extension_phone',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '20',
                    'comment' => 'Extension'
                ]
            );
        }

        $setup->endSetup();
    }

    private function _addAttributeToQuoteAddress($setup)
    {
        /** @var QuoteSetup $quoteSetup */
        $quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);
        $attributes = [
            'extension_phone'        => ['type' => Table::TYPE_TEXT],
        ];

        foreach ($attributes as $attributeCode => $attributeParams) {
            $quoteSetup->addAttribute('quote_address', $attributeCode, $attributeParams);
        }
    }

    private function _addAttributeToOrderAddress($setup)
    {
        /** @var SalesSetup $salesSetup */
        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
        $attributes = [
            'extension_phone'        => ['type' => Table::TYPE_TEXT],
        ];

        foreach ($attributes as $attributeCode => $attributeParams) {
            $salesSetup->addAttribute('order_address', $attributeCode, $attributeParams);
        }
    }
}