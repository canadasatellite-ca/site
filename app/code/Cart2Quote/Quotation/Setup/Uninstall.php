<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 * @codingStandardsIgnoreFile
 */

namespace Cart2Quote\Quotation\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

/**
 * Class Uninstall
 *
 * @package Cart2Quote\Quotation\Setup
 */
class Uninstall implements UninstallInterface
{
    /**
     * @var \Cart2Quote\Quotation\Setup\QuoteSetup
     */
    protected $quoteSetup;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Catalog\Setup\CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * @var \Cart2Quote\Quotation\Setup\SalesSetupFactory
     */
    private $salesSetupFactory;

    /**
     * @var \Magento\Authorization\Model\ResourceModel\Rules\Collection
     */
    private $rulesResourceModelCollection;

    /**
     * @var \Magento\Authorization\Model\ResourceModel\RulesFactory
     */
    private $rulesResourceModelFactory;

    /**
     * @var \Magento\SalesSequence\Model\ResourceModel\MetaFactory
     */
    private $metaResourceModelFactory;

    /**
     * @var \Magento\SalesSequence\Model\ResourceModel\ProfileFactory
     */
    private $profileResourceModelFactory;

    /**
     * Uninstall constructor.
     *
     * @param \Cart2Quote\Quotation\Setup\QuoteSetup $quoteSetup
     * @param \Magento\SalesSequence\Model\ResourceModel\ProfileFactory $profileResourceModelFactory
     * @param \Magento\SalesSequence\Model\ResourceModel\MetaFactory $metaResourceModelFactory
     * @param \Magento\Authorization\Model\ResourceModel\RulesFactory $rulesResourceModelFactory
     * @param \Magento\Authorization\Model\ResourceModel\Rules\Collection $rulesResourceCollection
     * @param \Cart2Quote\Quotation\Setup\SalesSetupFactory $salesSetupFactory
     * @param \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Cart2Quote\Quotation\Setup\QuoteSetup $quoteSetup,
        \Magento\SalesSequence\Model\ResourceModel\ProfileFactory $profileResourceModelFactory,
        \Magento\SalesSequence\Model\ResourceModel\MetaFactory $metaResourceModelFactory,
        \Magento\Authorization\Model\ResourceModel\RulesFactory $rulesResourceModelFactory,
        \Magento\Authorization\Model\ResourceModel\Rules\Collection $rulesResourceCollection,
        \Cart2Quote\Quotation\Setup\SalesSetupFactory $salesSetupFactory,
        \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->quoteSetup = $quoteSetup;
        $this->storeManager = $storeManager;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->rulesResourceModelCollection = $rulesResourceCollection;
        $this->rulesResourceModelFactory = $rulesResourceModelFactory;
        $this->metaResourceModelFactory = $metaResourceModelFactory;
        $this->profileResourceModelFactory = $profileResourceModelFactory;
    }

    /**
     * Uninstall
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function uninstall(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
        $this->dropTables($setup);
        $this->dropColumns($setup);

        $this->removeAclResources();
        $this->removeProductAttribute($setup);
        $this->removeSaleSequence($setup);
        $this->removeConfigurationValues($setup);

        $setup->endSetup();
    }

    /**
     * Remove acl rules related to cart2quote quotation
     */
    private function removeAclResources()
    {
        $this->rulesResourceModelCollection->addFieldToFilter('resource_id', [['like' => '%Cart2Quote_Quotation::%']]);
        /**
         * @var \Magento\Authorization\Model\Rules $rule
         */
        foreach ($this->rulesResourceModelCollection->getItems() as $rule) {
            $this->rulesResourceModelFactory->create()->delete($rule);
        }
    }

    /**
     * Remove product attribute
     *
     * @param SchemaSetupInterface $setup
     */
    private function removeProductAttribute(SchemaSetupInterface $setup)
    {
        /**
         * @var \Magento\Catalog\Setup\CategorySetup $catalogSetup
         */
        $catalogSetup = $this->categorySetupFactory->create();
        $catalogAttributeId = $catalogSetup->getAttributeId(
            \Magento\Catalog\Model\Product::ENTITY,
            'cart2quote_quotable'
        );
        $catalogSetup->removeAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'cart2quote_quotable'
        );
        $this->quoteSetup->getConnection()->delete(
            $this->quoteSetup->getTable('catalog_eav_attribute'),
            ['attribute_id = ?' => $catalogAttributeId]
        );
    }

    /**
     * Function to remove Sale Sequences
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function removeSaleSequence(SchemaSetupInterface $setup)
    {
        /**
         * Remove entity type and sales sequence tables
         *
         * @var \Cart2Quote\Quotation\Setup\SalesSetup $salesSetup
         */
        $salesSetup = $this->salesSetupFactory->create();
        foreach ($salesSetup->getDefaultEntities() as $entityType => $entity) {
            $salesSetup->removeEntityType($entityType);
            foreach ($this->storeManager->getStores(true) as $store) {
                /**
                 * @var \Magento\SalesSequence\Model\ResourceModel\Meta $metaResourceModel
                 */
                $metaResourceModel = $this->metaResourceModelFactory->create();
                $meta = $metaResourceModel->loadByEntityTypeAndStore($entityType, $store->getId());

                /**
                 * @var \Magento\SalesSequence\Model\ResourceModel\Profile $profileResourceModel
                 */
                $profileResourceModel = $this->profileResourceModelFactory->create();
                $profile = $profileResourceModel->loadActiveProfile($meta->getId());

                $metaResourceModel->delete($meta);
                $profileResourceModel->delete($profile);

                $sequenceTable = $salesSetup->getTable(sprintf('sequence_%s_%s', $entityType, $store->getId()));
                $this->quoteSetup->dropTable($sequenceTable);
            }
        }
    }

    /**
     * Remove configuration values
     *
     * @param SchemaSetupInterface $setup
     */
    private function removeConfigurationValues(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->delete(
            $this->quoteSetup->getTable('core_config_data'),
            ['path LIKE ?' => 'cart2quote_quotation%']
        );
    }

    /**
     * Drop tables
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    private function dropTables(SchemaSetupInterface $setup)
    {
        $this->quoteSetup->dropTable($this->quoteSetup->getTable('quotation_quote'));
        $this->quoteSetup->dropTable($this->quoteSetup->getTable('quotation_quote_sections'));
        $this->quoteSetup->dropTable($this->quoteSetup->getTable('quotation_quote_section_items'));
        $this->quoteSetup->dropTable($this->quoteSetup->getTable('quotation_quote_status'));
        $this->quoteSetup->dropTable($this->quoteSetup->getTable('quotation_quote_status_history'));
        $this->quoteSetup->dropTable($this->quoteSetup->getTable('quotation_quote_status_label'));
        $this->quoteSetup->dropTable($this->quoteSetup->getTable('quotation_quote_status_state'));
        $this->quoteSetup->dropTable($this->quoteSetup->getTable('quotation_quote_tier_item'));
        $this->quoteSetup->dropTable($this->quoteSetup->getTable('quotation_aggregated'));
    }

    /**
     * Drop columns
     *
     * @param SchemaSetupInterface $setup
     */
    private function dropColumns(SchemaSetupInterface $setup)
    {
        $this->quoteSetup->dropColumn($this->quoteSetup->getTable('quote'), 'is_quotation_quote');
        $this->quoteSetup->dropColumn($this->quoteSetup->getTable('quote'), 'linked_quotation_id');
    }
}
