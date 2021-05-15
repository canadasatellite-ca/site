<?php

namespace Interactivated\Quotecheckout\Observer;

class CustomQuoteProcess implements \Magento\Framework\Event\ObserverInterface{
    protected $request;
    protected $session;
    function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Cart2Quote\Quotation\Model\Session $session
    )
    {
        $this->request = $request;
        $this->session = $session;
    }

    function execute(\Magento\Framework\Event\Observer $observer)
    {
        $checkout_session = $observer->getEvent()->getData('checkout_session');
        $name = $this->request->getModuleName();
        if($name=='quotecheckout' && !$checkout_session->hasQuote()){
            //$quote = $this->session->getQuote();
            //$checkout_session->replaceQuote($quote);
        }
        return $this;
    }
}