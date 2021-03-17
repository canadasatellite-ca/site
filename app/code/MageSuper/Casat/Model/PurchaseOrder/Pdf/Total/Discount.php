<?php
/**
 * Cart2Quote
 */

namespace Cart2Quote\Quotation\Model\Quote\Pdf\Total;

/**
 * Class Discount
 * @package Cart2Quote\Quotation\Model\Quote\Pdf\Total
 */
class Discount extends DefaultTotal
{
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
     * @return \Magento\Tax\Model\Config
     */
    public function getTaxConfig()
    {
        return $this->_taxConfig;
    }

    /**
     * @return array
     */
    public static function getUnderscoreCache()
    {
        return self::$_underscoreCache;
    }

    /**
     * @return \Magento\Tax\Model\Calculation
     */
    public function getTaxCalculation()
    {
        return $this->_taxCalculation;
    }

    /**
     * @return \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory
     */
    public function getTaxOrdersFactory()
    {
        return $this->_taxOrdersFactory;
    }

    /**
     * @return mixed
     */
    public function getQuote()
    {
        return $this->_quote;
    }

    /**
     * @return \Magento\Tax\Helper\Data
     */
    public function getTaxHelper()
    {
        return $this->_taxHelper;
    }

    /**
     * Get discounts for display on PDF
     * @return array
     */
    public function getTotalsForDisplay()
    {
        $totals = [];
        $amount = 0;
        $quote = $this->getSource();
        $couponCode = $quote->getCouponCode();

        $title = __($this->getTitle());
        $label = null;
        if ($couponCode != '') {
            $label = $title . ' (' . $couponCode . '):';
            $amount = $this->getAmount();
        }
        $totals = array_merge($totals, parent::getTotalsForDisplay());

        //being a discount we want the outcome to be negative
        if ($amount > 0) {
            $amount = $amount - ($amount * 2);
        }

        if ($label != null) {
            $totals[0]['amount'] = $quote->formatPriceTxt($amount);
            $totals[0]['label'] = $label;
        }
        return $totals;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->getSource()->getBaseSubtotalWithDiscount() - $this->getSource()->getBaseSubtotal();
    }
}
