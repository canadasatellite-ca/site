<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Pdf\Total;

/**
 * Class Discount
 *
 * @package Cart2Quote\Quotation\Model\Quote\Pdf\Total
 */
class Discount extends DefaultTotal
{
    use \Cart2Quote\Features\Traits\Model\Quote\Pdf\Total\Discount {
        getUnderscoreCache as private traitGetUnderscoreCache;
        getTaxConfig as private traitGetTaxConfig;
        getTaxCalculation as private traitGetTaxCalculation;
        getTaxOrdersFactory as private traitGetTaxOrdersFactory;
        getQuote as private traitGetQuote;
        getTaxHelper as private traitGetTaxHelper;
        getTotalsForDisplay as private traitGetTotalsForDisplay;
        getAmount as private traitGetAmount;
    }

    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $_taxConfig;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote
     */
    protected $_quote;

    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxHelper;

    /**
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Magento\Tax\Model\Calculation $taxCalculation
     * @param \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $ordersFactory
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Tax\Model\Calculation $taxCalculation,
        \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $ordersFactory,
        \Magento\Tax\Model\Config $taxConfig,
        array $data = []
    ) {
        $this->_taxConfig = $taxConfig;
        $this->_taxHelper = $taxHelper;
        parent::__construct($taxHelper, $taxCalculation, $ordersFactory, $data);
    }

    /**
     * Get the underscore cache
     *
     * @return array
     */
    public static function getUnderscoreCache()
    {
        return Discount::traitGetUnderscoreCache();
    }

    /**
     * Get the tax config
     *
     * @return \Magento\Tax\Model\Config
     */
    public function getTaxConfig()
    {
        return $this->traitGetTaxConfig();
    }

    /**
     * Get tax calculation
     *
     * @return \Magento\Tax\Model\Calculation
     */
    public function getTaxCalculation()
    {
        return $this->traitGetTaxCalculation();
    }

    /**
     * Get tax orders factory
     *
     * @return \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory
     */
    public function getTaxOrdersFactory()
    {
        return $this->traitGetTaxOrdersFactory();
    }

    /**
     * Get quote
     *
     * @return mixed
     */
    public function getQuote()
    {
        return $this->traitGetQuote();
    }

    /**
     * Get the tax helper
     *
     * @return \Magento\Tax\Helper\Data
     */
    public function getTaxHelper()
    {
        return $this->traitGetTaxHelper();
    }

    /**
     * Get discounts for display on PDF
     *
     * @return array
     */
    public function getTotalsForDisplay()
    {
        return $this->traitGetTotalsForDisplay();
    }

    /**
     * Get the total
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->traitGetAmount();
    }
}
