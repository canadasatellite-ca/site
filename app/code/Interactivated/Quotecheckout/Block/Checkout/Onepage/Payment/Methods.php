<?php

namespace Interactivated\Quotecheckout\Block\Checkout\Onepage\Payment;

class Methods extends \Magento\Payment\Block\Form\Container
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param \Magento\Payment\Model\Checks\SpecificationFactory $methodSpecificationFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Payment\Model\Checks\SpecificationFactory $methodSpecificationFactory,
        \Cart2Quote\Quotation\Model\Session $checkoutSession,
        array $data = []
    ) {
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($context, $paymentHelper, $methodSpecificationFactory, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    function getQuote()
    {
        return $this->_checkoutSession->getQuote();
    }

    /**
     * Check payment method model
     *
     * @param \Magento\Payment\Model\MethodInterface $method
     * @return bool
     */
    protected function _canUseMethod($method)
    {
        return $method && $method->canUseCheckout() && parent::_canUseMethod($method);
    }

    /**
     * Retrieve code of current payment method
     *
     * @return mixed
     */
    function getSelectedMethodCode()
    {
        $method = $this->getQuote()->getPayment()->getMethod();
        if ($method) {
            return $method;
        }
        return false;
    }

    /**
     * Payment method form html getter
     *
     * @param \Magento\Payment\Model\MethodInterface $method
     * @return string
     */
    function getPaymentMethodFormHtml(\Magento\Payment\Model\MethodInterface $method)
    {
        return $this->getChildHtml('payment.method.' . $method->getCode());
    }

    /**
     * Return method title for payment selection page
     *
     * @param \Magento\Payment\Model\MethodInterface $method
     * @return string
     */
    function getMethodTitle(\Magento\Payment\Model\MethodInterface $method)
    {
        $form = $this->getChildBlock('payment.method.' . $method->getCode());
        if ($form && $form->hasMethodTitle()) {
            return $form->getMethodTitle();
        }
        return $method->getTitle();
    }

    /**
     * Payment method additional label part getter
     *
     * @param \Magento\Payment\Model\MethodInterface $method
     * @return string
     */
    function getMethodLabelAfterHtml(\Magento\Payment\Model\MethodInterface $method)
    {
        $form = $this->getChildBlock('payment.method.' . $method->getCode());
        if ($form) {
            return $form->getMethodLabelAfterHtml();
        }
    }
}
