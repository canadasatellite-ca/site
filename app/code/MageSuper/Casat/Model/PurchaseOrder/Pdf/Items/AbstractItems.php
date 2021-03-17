<?php
/**
 * Cart2Quote
 */
namespace MageSuper\Casat\Model\PurchaseOrder\Pdf\Items;

/**
 * Quote Pdf Items renderer Abstract
 */
abstract class AbstractItems extends \Magento\Sales\Model\Order\Pdf\Items\AbstractItems
{
    /**
     * Core string
     *
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    /**
     * Set Quote model
     *
     * @param  \Cart2Quote\Quotation\Model\Quote
     * @return $this
     */
    public function setQuote(\Magestore\PurchaseOrderSuccess\Model\PurchaseOrder $quote)
    {
        $this->_quote = $quote;

        return $this;
    }

    /**
     * Retrieve quote object
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        if (null === $this->_quote) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The quote object is not specified.'));
        }

        return $this->_quote;
    }
}
