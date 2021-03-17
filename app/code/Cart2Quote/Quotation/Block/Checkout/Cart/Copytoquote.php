<?php
/**
 * Cart2Quote
 */
namespace Cart2Quote\Quotation\Block\Checkout\Cart;

/**
 * One page checkout success page
 */
class Copytoquote extends \Magento\Framework\View\Element\Template
{
    /**
     * @var bool
     */
    protected $_visibilityEnabled;

    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $cart2QuoteHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Cart2Quote\Quotation\Helper\Data $cart2QuoteHelper,
        array $data = []
    ) {
        $this->cart2QuoteHelper = $cart2QuoteHelper;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Check if Cart2Quote visibility is enabled
     * @return bool
     */
    public function getIsQuotationEnabled()
    {
        if (isset($this->_visibilityEnabled)) {
            return $this->_visibilityEnabled;
        }

        $enabled = $this->_scopeConfig->getValue(
            'cart2quote_quotation/global/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($enabled) {
            $this->_visibilityEnabled = true;
            return true;
        }

        $this->_visibilityEnabled = false;
        return false;
    }
    /**
     * Check hide order references
     * @return boolean
     */
    public function getShowOrderReferences()
    {
        return $this->cart2QuoteHelper->getShowOrderReferences();
    }
}
