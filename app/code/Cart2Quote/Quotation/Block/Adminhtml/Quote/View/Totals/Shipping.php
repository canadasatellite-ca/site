<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Totals;

/**
 * Class Shipping
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Totals
 */
class Shipping extends \Magento\Sales\Block\Adminhtml\Order\Create\Totals\Shipping
{
    /**
     * @var \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     */
    protected $quoteFactory;

    /**
     * Shipping constructor
     *
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Sales\Helper\Data $salesData
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Sales\Helper\Data $salesData,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Tax\Model\Config $taxConfig,
        array $data = []
    ) {
        $this->quoteFactory = $quoteFactory;
        parent::__construct($context,
            $sessionQuote,
            $orderCreate,
            $priceCurrency,
            $salesData,
            $salesConfig,
            $taxConfig,
            $data
        );
    }

    /**
     * Format price function
     *
     * @param float $value
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function formatPrice($value)
    {
        return $this->getLayout()->getBlock('totals')->formatPrice($value);
    }

    /**
     * Get quote overwrite so that we get the quotation quote instead of the sales quote
     *
     * @return \Cart2Quote\Quotation\Model\Quote|\Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        $quotationQuoteId = $this->_sessionQuote->getQuotationQuoteId();
        return $this->quoteFactory->create()->load($quotationQuoteId);
    }
}
