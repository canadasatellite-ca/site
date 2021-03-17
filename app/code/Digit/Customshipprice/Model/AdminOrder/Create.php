<?php
	/**
		* Copyright © 2016 Magento. All rights reserved.
		* See COPYING.txt for license details.
	*/
	
	// @codingStandardsIgnoreFile
	
	namespace Digit\Customshipprice\Model\AdminOrder;
	
	/**
		* Order create model
		* @SuppressWarnings(PHPMD.TooManyFields)
		* @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
		* @SuppressWarnings(PHPMD.CouplingBetweenObjects)
	*/
	class Create extends \Magento\Sales\Model\AdminOrder\Create
	{
		
		/**
			* Parse data retrieved from request
			*
			* @param   array $data
			* @return  $this
			* @SuppressWarnings(PHPMD.CyclomaticComplexity)
			* @SuppressWarnings(PHPMD.NPathComplexity)
		*/
		public function importPostData($data)
		{
			if (is_array($data)) {
				$this->addData($data);
				} else {
				return $this;
			}
			
			if (isset($data['account'])) {
				$this->setAccountData($data['account']);
			}
			
			if (isset($data['comment'])) {
				$this->getQuote()->addData($data['comment']);
				if (empty($data['comment']['customer_note_notify'])) {
					$this->getQuote()->setCustomerNoteNotify(false);
					} else {
					$this->getQuote()->setCustomerNoteNotify(true);
				}
			}
			
			if (isset($data['billing_address'])) {
				$this->setBillingAddress($data['billing_address']);
			}
			
			if (isset($data['shipping_address'])) {
				$this->setShippingAddress($data['shipping_address']);
			}
			
			if (isset($data['shipping_method'])) {
				$this->setShippingMethod($data['shipping_method']);
			}
			
			if (isset($data['payment_method'])) {
				$this->setPaymentMethod($data['payment_method']);
			}
			
			if (isset($data['coupon']['code'])) {
				$this->applyCoupon($data['coupon']['code']);
			}
			
			
			$reinit_rates = false;
			
			if (isset($data['shipping_amount'])) {            	
				$shippingPrice = $this->_parseShippingPrice($data['shipping_amount']);
				//$this->getQuote()->getShippingAddress()->setShippingAmount($shippingPrice);
				$this->_session->setCustomshippriceAmount($shippingPrice);
				$reinit_rates = true;
			}
			
			if (isset($data['base_shipping_amount'])) {
				$baseShippingPrice = $this->_parseShippingPrice($data['base_shipping_amount']);
				//$this->getQuote()->getShippingAddress()->setBaseShippingAmount($baseShippingPrice, true);
				$this->_session->setCustomshippriceBaseAmount($baseShippingPrice);
				$reinit_rates = true;
			}
			
			if (isset($data['shipping_description'])) {
				//$this->getQuote()->getShippingAddress()->setShippingDescription($data['shipping_description']);
				$this->_session->setCustomshippriceDescription($data['shipping_description']);
				$reinit_rates = true;
			}
			
			if (isset($data['coupon']['code'])) {
				$this->applyCoupon($data['coupon']['code']);
				$reinit_rates = true;
			}
			
			if($reinit_rates)
			{
				//$this->collectShippingRates();
				//$this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
				//$this->collectRates();
				//$this->getQuote()->collectTotals();
			}
			return $this;
		}
		
		protected function _parseShippingPrice($price)
		{
			
			//$resolver = $this->_objectManager->get('Magento\Framework\Locale\Resolver');
			
			//$price = $resolver->getNumber($price);
			$price = $price>0 ? $price : 0;
			return $price;
		}
	}
