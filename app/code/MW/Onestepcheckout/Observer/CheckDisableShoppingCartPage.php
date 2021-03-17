<?php

namespace MW\Onestepcheckout\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckDisableShoppingCartPage implements ObserverInterface
{
	/**
	 * @var \MW\Onestepcheckout\Helper\Data
	 */
	protected $_dataHelper;

	/**
	 * @var \Magento\Framework\UrlInterface
	 */
	protected $_urlManager;

	/**
	 * @param \MW\Onestepcheckout\Helper\Data $dataHelper
	 * @param \Magento\Framework\UrlInterface $urlManager
	 */
	public function __construct(
		\MW\Onestepcheckout\Helper\Data $dataHelper,
		\Magento\Framework\UrlInterface $urlManager
	) {
		$this->_dataHelper = $dataHelper;
		$this->_urlManager = $urlManager;
	}

	/**
	 * Check redirect to checkout page after adding a product to cart
	 *
	 * @param  \Magento\Framework\Event\Observer $observer
	 * @return $this
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		if (!$this->_dataHelper->enabledInFrontend()) {
			return $this;
		}

		if ($this->_dataHelper->getStoreConfig('onestepcheckout/general/disable_shop_cart')) {
			$checkoutUrl = $this->_urlManager->getUrl('checkout');
			$result['backUrl'] = $this->_urlManager->getRedirectUrl($checkoutUrl);

			echo json_encode($result);
			exit;
		}

		return $this;
	}
}
