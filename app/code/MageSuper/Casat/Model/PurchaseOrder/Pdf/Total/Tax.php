<?php
/**
 * Cart2Quote
 */
namespace Cart2Quote\Quotation\Model\Quote\Pdf\Total;

/**
 * Class Tax
 * @package Cart2Quote\Quotation\Model\Quote\Pdf\Total
 */
class Tax extends DefaultTotal
{
    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $_taxConfig;

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
    function __construct(
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
     * Get tax amount for display on PDF
     * @return array
     */
    function getTotalsForDisplay()
    {
        $quote = $this->getQuote();
        $totals = [];
        $store = $this->getSource()->getStore();
        if ($this->_taxConfig->displaySalesTaxWithGrandTotal($store)) {
            return [];
        }
        $tax = 0;
        foreach ($quote->getItemsCollection() as $item) {
            $tax = $tax + $item->getTaxAmount();
        }

        if ($this->_taxConfig->displaySalesFullSummary($store)) {
            $totals = $this->getFullTaxInfo();
        }

        $tax = $quote->formatPriceTxt($tax);
        $totals = array_merge($totals, parent::getTotalsForDisplay());
        $totals[0]['amount'] = $tax;

        return $totals;
    }
}
