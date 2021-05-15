<?php

namespace Interactivated\Quotecheckout\Block\Checkout\Onepage\Billing;

class Sortbilling extends \Interactivated\Quotecheckout\Block\Checkout\Onepage\Billing
{
	function isRequired($addressName)
	{
		$status = $this->_dataHelper->getStoreConfig('onestepcheckout/addfield/'.$addressName);
		if ($status == '2') {
			return "required-entry";
		} else {
			return "";
		}
	}

	function isStar($addressName)
	{
		$status = $this->_dataHelper->getStoreConfig('onestepcheckout/addfield/'.$addressName);
		if ($status == '2') {
			return "*";
		} else {
			return "";
		}
	}

	function isDisable($name)
	{
		$status = $this->_dataHelper->getStoreConfig('onestepcheckout/addfield/'.$name);
		if ($status == '0') {
			return true;
		} else {
			return false;
		}
	}

	function getCustomerSession()
	{
		return $this->_customerSession;
	}
}
