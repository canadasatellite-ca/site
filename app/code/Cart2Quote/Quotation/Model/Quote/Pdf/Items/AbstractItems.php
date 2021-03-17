<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Pdf\Items;

/**
 * Quote Pdf Items renderer Abstract
 */
abstract class AbstractItems extends \Magento\Sales\Model\Order\Pdf\Items\AbstractItems
{
    use \Cart2Quote\Features\Traits\Model\Quote\Pdf\Items\AbstractItems {
        setQuote as private traitSetQuote;
        getQuote as private traitGetQuote;
        getQuoteTierItemPricesForDisplay as private traitGetQuoteTierItemPricesForDisplay;
    }

    /**
     * Core string
     *
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote
     */
    protected $quote;

    /**
     * @var \Cart2Quote\Quotation\Helper\QuotationTaxHelper
     */
    protected $quotationTaxHelper;

    /**
     * AbstractItems constructor.
     *
     * @param \Cart2Quote\Quotation\Helper\QuotationTaxHelper $quotationTaxHelper
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\QuotationTaxHelper $quotationTaxHelper,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->quotationTaxHelper = $quotationTaxHelper;
        parent::__construct(
            $context,
            $registry,
            $taxData,
            $filesystem,
            $filterManager,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Set Quote model
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return $this
     */
    public function setQuote(\Cart2Quote\Quotation\Model\Quote $quote)
    {
        return $this->traitSetQuote($quote);
    }

    /**
     * Retrieve quote object
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        return $this->traitGetQuote();
    }

    /**
     * Get the tier item prices to display
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getQuoteTierItemPricesForDisplay()
    {
        return $this->traitGetQuoteTierItemPricesForDisplay();
    }
}
