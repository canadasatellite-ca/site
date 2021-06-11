<?php
namespace Schogini\Beanstream\Model;
use Magento\Framework\Exception\CouldNotSaveException;
# 2021-06-11 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
# "Refactor the `Schogini_Beanstream` module": https://github.com/canadasatellite-ca/site/issues/176
class PaymentManagement extends \Magento\Checkout\Model\PaymentInformationManagement{
	function savePaymentInformationAndPlaceOrder(
		$spb2524e
		,\Magento\Quote\Api\Data\PaymentInterface $spf269e4
		,\Magento\Quote\Api\Data\AddressInterface $spc02997 = null
	)
	{
		$this->savePaymentInformation($spb2524e, $spf269e4, $spc02997);
		try {
			$spf504cb = $this->cartManagement->placeOrder($spb2524e);
		} catch (\Exception $sp8303e0) {
			throw new CouldNotSaveException(__('Cannot place order: ' . $sp8303e0->getMessage()), $sp8303e0);
		}
		return $spf504cb;
	}
}