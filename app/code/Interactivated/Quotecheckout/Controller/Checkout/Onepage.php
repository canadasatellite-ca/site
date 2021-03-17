<?php

namespace Interactivated\Quotecheckout\Controller\Checkout;

class Onepage extends \Magento\Checkout\Controller\Onepage
{
    /**
     * @var \Interactivated\Quotecheckout\Helper\Data
     */
    protected $_dataHelper = null;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession = null;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_sessionManager = null;

    public function execute()
    {
        parent::execute();
    }

    /**
     * Define properties for methods
     */
    public function defineProperties()
    {
        $this->_dataHelper      = $this->_objectManager->get('Interactivated\Quotecheckout\Helper\Data');
        $this->_checkoutSession = $this->_objectManager->get('Cart2Quote\Quotation\Model\Session');
        $this->_sessionManager  = $this->_objectManager->get('Magento\Framework\Session\SessionManagerInterface');
    }

    /**
     * Get checkout session
     *
     * @return \Magento\Checkout\Model\Session
     */
    public function getCheckout()
    {
        return $this->_objectManager->get('Cart2Quote\Quotation\Model\Session');
    }

    /**
     * Get checkout quote instance by current session
     *
     * @return Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    /**
     * Get checkout quote instance by current cart
     *
     * @return Quote
     */
    protected function _getQuote()
    {
        return $this->_objectManager->get('Cart2Quote\Quotation\Model\QuotationCart')->getQuote();
    }

    public function getOnepage()
    {
        return $this->_objectManager->get('Interactivated\Quotecheckout\Model\Checkout\Type\Onepage');
    }
    /**
     * Clear billing form session
     */
    public function clearBillingSession()
    {
        if ($this->_sessionManager->getCountryId()) {
            $this->_sessionManager->unsCountryId();
        }

        if ($this->_sessionManager->getPostcode()) {
            $this->_sessionManager->unsPostcode();
        }

        if ($this->_sessionManager->getRegion()) {
            $this->_sessionManager->unsRegion();
        }

        if ($this->_sessionManager->getRegionId()) {
            $this->_sessionManager->unsRegionId();
        }

        if ($this->_sessionManager->getCity()) {
            $this->_sessionManager->unsCity();
        }
    }

}
