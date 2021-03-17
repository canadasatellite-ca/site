<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Pdf\Total;

/**
 * Class Shipping
 *
 * @package Cart2Quote\Quotation\Model\Quote\Pdf\Total
 */
class Shipping extends DefaultTotal
{
    use \Cart2Quote\Features\Traits\Model\Quote\Pdf\Total\Shipping {
        getTotalsForDisplay as private traitGetTotalsForDisplay;
        getSource as private traitGetSource;
    }

    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $_taxConfig;

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
        parent::__construct($taxHelper, $taxCalculation, $ordersFactory, $data);
    }

    /**
     * Get shipping totals for display on PDF
     *
     * @return array
     */
    public function getTotalsForDisplay()
    {
        return $this->traitGetTotalsForDisplay();
    }

    /**
     * Get shipping address from source data
     *
     * @return \Magento\Quote\Model\Quote\Address
     */
    public function getSource()
    {
        return $this->traitGetSource();
    }
}
