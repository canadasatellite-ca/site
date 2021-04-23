<?php


namespace MageSuper\Casat\Observer;

class QuoteAddressSaveBefore implements \Magento\Framework\Event\ObserverInterface
{
    protected $scopeConfig;

    /**
     * Init
     *
     * @param CategorySetupFactory $categorySetupFactory
     * @param EavSetupFactory $eavSetupFactory
     */
    function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote\Address $quote_address */
        $quote_address = $observer->getData('quote_address');
        $region = $quote_address->getRegion();
        if (is_array($region)){
            $quote_address->setRegion($region['region']);
        }
    }
}
