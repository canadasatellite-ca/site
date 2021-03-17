<?php

namespace MW\Onestepcheckout\Observer;

use Magento\Framework\Event\ObserverInterface;

class CollectCustomItems implements ObserverInterface
{
	/**
	 * @var \MW\Onestepcheckout\Helper\Data
	 */
	protected $_dataHelper;

	/**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_sessionManager;

    /**
     * @param \MW\Onestepcheckout\Helper\Data $dataHelper
     * @param \Magento\Framework\Session\SessionManagerInterface $sessionManager
     */
    public function __construct(
    	\MW\Onestepcheckout\Helper\Data $dataHelper,
    	\Magento\Framework\Session\SessionManagerInterface $sessionManager
    ) {
    	$this->_dataHelper = $dataHelper;
    	$this->_sessionManager = $sessionManager;
    }

	/**
	 * Add Gift Wrap amount to paypal payment
	 *
	 * @param  \Magento\Framework\Event\Observer $observer
	 * @return $this
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$isWrap = $this->_sessionManager->getIsWrap();
        if ($isWrap) {
            $cart = $observer->getEvent()->getCart();
            $baseGiftWrap = $this->_dataHelper->getStoreConfig('onestepcheckout/addfield/price_gift_wrap');
            $cart->addCustomItem('Gift Wrap', 1, $baseGiftWrap);
        }

        return $this;
	}
}
