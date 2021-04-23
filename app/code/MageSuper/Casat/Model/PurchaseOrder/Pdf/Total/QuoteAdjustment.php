<?php
/**
 * Cart2Quote
 */
namespace Cart2Quote\Quotation\Model\Quote\Pdf\Total;

/**
 * Class QuoteReduction
 * @package Cart2Quote\Quotation\Model\Quote\Pdf\Total
 */
class QuoteAdjustment extends \Cart2Quote\Quotation\Model\Quote\Pdf\Total\DefaultTotal
{
    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $taxConfig;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Magento\Tax\Model\Calculation $taxCalculation
     * @param \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $ordersFactory
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    function __construct(
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Tax\Model\Calculation $taxCalculation,
        \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $ordersFactory,
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->taxConfig = $taxConfig;
        parent::__construct($taxHelper, $taxCalculation, $ordersFactory, $data);
    }

    /**
     * Get Quote Reduction for display on PDF
     * @return array
     */
    function getTotalsForDisplay()
    {
        $totals = parent::getTotalsForDisplay();
        $showAdjustment = $this->scopeConfig->getValue(\Cart2Quote\Quotation\Block\Quote\Totals::XML_PATH_CART2QUOTE_QUOTATION_GLOBAL_SHOW_QUOTE_ADJUSTMENT);
        if (intval($showAdjustment) == 0 || (intval($showAdjustment) == 2 && $this->getAmount() == 0)) {
            unset($totals[0]);
        }

        return $totals;
    }

    /**
     * Function to return the amount that should be included in QuoteReduction block
     * @return mixed
     */
    function getAmount()
    {
        $amount = $this->getSource()->getSubtotal() - $this->getSource()->getOriginalSubTotal();

        return $amount;
    }
}
