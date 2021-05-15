<?php

namespace Interactivated\Quotecheckout\Block\Checkout\Onepage\Review;

class Info extends \Magento\Sales\Block\Items\AbstractItems
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Cart2Quote\Quotation\Model\Session $checkoutSession,
        array $data = []
    ) {
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * @return array
     */
    function getItems()
    {
        return $this->_checkoutSession->getQuote()->getAllVisibleItems();
    }

    /**
     * @return array
     */
    function getTotals()
    {
        return $this->_checkoutSession->getQuote()->getTotals();
    }
}
