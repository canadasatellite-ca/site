<?php

	namespace Digit\Paymentmethods\Model\Method;
	class Wiretransfer extends \Magento\Payment\Model\Method\AbstractMethod
	{
		protected $_code = 'wiretransfer';
		protected $_isOffline = true;
	}	