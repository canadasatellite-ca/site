<?php

	namespace Digit\Paymentmethods\Model\Method;
	class Cash extends \Magento\Payment\Model\Method\AbstractMethod
	{
		protected $_code = 'cash';
		protected $_isOffline = true;
	}	