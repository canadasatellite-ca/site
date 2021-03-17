<?php


namespace MageSuper\Casat\Observer;

class CartSaveBefore implements \Magento\Framework\Event\ObserverInterface
{
    protected $scopeConfig;

    /**
     * Init
     *
     * @param CategorySetupFactory $categorySetupFactory
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Checkout\Model\Cart $cart */
        $cart = $observer->getData('cart');
        $address = $cart->getQuote()->getShippingAddress();
        if (!$address->getCountryId()){
            $address->setCountryId($this->scopeConfig->getValue('general/country/default'));
        }
    }
}
