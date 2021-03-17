<?php

	namespace Digit\Paymentmethods\Model\Method;
	class Checkbymail extends \Magento\Payment\Model\Method\AbstractMethod
	{
		protected $_code = 'checkbymail';
		protected $_isOffline = true;
	}	