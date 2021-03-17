<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Quote;

use Cart2Quote\Quotation\Model\Quote;

/**
 * Class Items
 *
 * @package Cart2Quote\Quotation\Block\Quote
 */
class Items extends \Magento\Sales\Block\Items\AbstractItems
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Quotation Helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * Tier item collection
     *
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection
     */
    protected $tierItemCollection;
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\SectionFactory
     */
    private $sectionFactory;

    /**
     * Items constructor.
     *
     * @param \Cart2Quote\Quotation\Model\Quote\SectionFactory $sectionFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection $tierItemCollection
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Quote\SectionFactory $sectionFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection $tierItemCollection,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->quotationHelper = $quotationHelper;
        $this->tierItemCollection = $tierItemCollection;
        parent::__construct($context, $data);
        $this->sectionFactory = $sectionFactory;
    }

    /**
     * Check disabled product comment field
     *
     * @return boolean
     */
    public function isProductRemarkDisabled()
    {
        return $this->quotationHelper->isProductRemarkDisabled();
    }

    /**
     * Check if an optional product exists on the quote
     *
     * @return int
     */
    public function hasOptionalProducts()
    {
        if (!$this->tierItemCollection->isLoaded()) {
            $tableName = $this->tierItemCollection->getTable('quote_item');
            $this->tierItemCollection
                ->addFieldToFilter('make_optional', true)
                ->join(
                    $tableName,
                    sprintf(
                        '`%s`.item_id = `main_table`.item_id AND `%s`.quote_id = %s',
                        $tableName,
                        $tableName,
                        $this->getQuote()->getId()
                    )
                );
        }

        return (bool)$this->tierItemCollection->getSize();
    }

    /**
     * Retrieve current quote model instance
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        return $this->_coreRegistry->registry('current_quote');
    }

    /**
     * Get sections
     *
     * @return array
     */
    public function getSections()
    {
        return $this->getQuote()->getSections();
    }

    /**
     * Get config setting for hide prices dashboard
     *
     * @param Quote $quote
     * @return bool
     */
    public function isHidePrices($quote)
    {
        return $this->quotationHelper->isHidePrices($quote);
    }
}
