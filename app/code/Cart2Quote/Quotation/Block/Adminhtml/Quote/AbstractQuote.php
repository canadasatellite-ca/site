<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote;

use Cart2Quote\Quotation\Model\Quote;

/**
 * Adminhtml quote abstract block
 */
class AbstractQuote extends \Magento\Backend\Block\Widget
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Admin helper
     *
     * @var \Magento\Sales\Helper\Admin
     */
    protected $_adminHelper;

    /**
     * AbstractQuote constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        array $data = []
    ) {
        $this->_adminHelper = $adminHelper;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Display price attribute
     *
     * @param string $code
     * @param bool $strong
     * @param string $separator
     * @return string
     */
    public function displayPriceAttribute($code, $strong = false, $separator = '<br/>')
    {
        return $this->_adminHelper->displayPriceAttribute($this->getPriceDataObject(), $code, $strong, $separator);
    }

    /**
     * Get price data object
     *
     * @return Quote|mixed
     */
    public function getPriceDataObject()
    {
        $obj = $this->getData('price_data_object');
        if ($obj === null) {
            return $this->getQuote();
        }
        return $obj;
    }

    /**
     * Retrieve available quote
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getQuote()
    {
        if ($this->hasQuote()) {
            return $this->getData('quote');
        }
        if ($this->_coreRegistry->registry('current_quote')) {
            return $this->_coreRegistry->registry('current_quote');
        }
        if ($this->_coreRegistry->registry('quote')) {
            return $this->_coreRegistry->registry('quote');
        }
        throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t get the quote instance right now.'));
    }

    /**
     * Retrieve quote totals block settings
     *
     * @return array
     */
    public function getQuoteTotalData()
    {
        return [];
    }

    /**
     * Retrieve quote info block settings
     *
     * @return array
     */
    public function getQuoteInfoData()
    {
        return [];
    }

    /**
     * Retrieve subtotal price include tax html formated content
     *
     * @param \Magento\Framework\DataObject $quote
     * @return string
     */
    public function displayShippingPriceInclTax($quote)
    {
        $shipping = $quote->getShippingInclTax();
        if ($shipping) {
            $baseShipping = $quote->getBaseShippingInclTax();
        } else {
            $shipping = $quote->getShippingAmount() + $quote->getShippingTaxAmount();
            $baseShipping = $quote->getBaseShippingAmount() + $quote->getBaseShippingTaxAmount();
        }

        return $this->displayPrices($baseShipping, $shipping, false, ' ');
    }

    /**
     * Display prices
     *
     * @param float $basePrice
     * @param float $price
     * @param bool $strong
     * @param string $separator
     * @return string
     */
    public function displayPrices($basePrice, $price, $strong = false, $separator = '<br/>')
    {
        return $this->_adminHelper->displayPrices(
            $this->getPriceDataObject(),
            $basePrice,
            $price,
            $strong,
            $separator
        );
    }
}
