<?php
namespace Schogini\Beanstream\Model;
use Magento\Framework\Exception\CouldNotSaveException as CNSE;
use Magento\Quote\Api\Data\AddressInterface as IA;
use Magento\Quote\Api\Data\PaymentInterface as IP;
# 2021-06-11 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
# "Refactor the `Schogini_Beanstream` module": https://github.com/canadasatellite-ca/site/issues/176
class PaymentManagement extends \Magento\Checkout\Model\PaymentInformationManagement{
	function savePaymentInformationAndPlaceOrder($v, IP $p, IA $a = null) {
		$this->savePaymentInformation($v, $p, $a);
		try {$r = $this->cartManagement->placeOrder($v);}
		catch (\Exception $e) {throw new CNSE(__('Cannot place order: ' . $e->getMessage()), $e);}
		return $r;
	}
}