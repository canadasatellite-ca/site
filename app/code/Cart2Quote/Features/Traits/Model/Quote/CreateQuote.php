<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote;
use Cart2Quote\Quotation\Model\Quote;
/**
 * Trait CreateQuote
 *
 * @package Cart2Quote\Quotation\Model\Quote
 */
trait CreateQuote
{
    /**
     * Save the customer information for new customer
     *
     * @return void
     */
    private function saveNewCustomer()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quote = $this->getQuote();
        $billing = $quote->getBillingAddress();
        $shipping = $quote->isVirtual() ? null : $quote->getShippingAddress();
        if (isset($billing, $shipping) && $billing->validate() && $shipping->validate()
            || isset($billing) && $billing->validate() && $quote->isVirtual()
        ) {
            parent::_prepareNewCustomerQuote();
        }
        $customer = $quote->getCustomer();
        //merge customer data from billing and shipping address to quote customer
        $customer = $this->mergeCustomerData($customer, $quote);
        //use this try catch setup to get the correct error in the logs
        try {
            if (!$this->registry->registry('isSecureArea')) {
                $this->registry->register('isSecureArea', true);
            }
            $customer = $this->customerRepository->save($customer);
            $this->accountManagement->sendNewEmailConfirmation($customer);
            $quote->setCustomer($customer);
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Error while saving the customer')
            );
        } finally {
            $this->registry->unregister('isSecureArea');
        }
		}
	}
    /**
     * Function that fills the customer object with data from the quote and billing and shipping address
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param Quote $quote
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    private function mergeCustomerData(
        $customer,
        $quote
    ) {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$billing = $quote->getBillingAddress();
        $shipping = $quote->isVirtual() ? null : $quote->getShippingAddress();
        $dataToCopy = [
            'prefix' => [
                'getPrefix',
                'setPrefix',
                'getPrefix',
                'getCustomerPrefix'
            ],
            'middlename' => [
                'getMiddlename',
                'setMiddlename',
                'getMiddlename',
                'getCustomerMiddlename'
            ],
            'suffix' => [
                'getSuffix',
                'setSuffix',
                'getSuffix',
                'getCustomerSuffix'
            ],
            'dob' => [
                'getDob',
                'setDob',
                'getDob',
                'getCustomerDob'
            ],
            'taxvat' => [
                'getTaxvat',
                'setTaxvat',
                'getVatId',
                'getCustomerTaxvat'
            ],
            'gender' => [
                'getGender',
                'setGender',
                'getGender',
                'getCustomerGender'
            ]
        ];
        foreach ($dataToCopy as $field) {
            $customerGetter = $field[0];
            $customerSetter = $field[1];
            $addressGetter = $field[2];
            $quoteGetter = $field[3];
            if (!$customer->$customerGetter()) {
                $customer->$customerSetter($quote->$quoteGetter());
                if (!$customer->$customerGetter()) {
                    $customer->$customerSetter($billing->$addressGetter());
                    if (!$customer->$customerGetter() && ($shipping != null)) {
                        $customer->$customerSetter($shipping->$addressGetter());
                    }
                }
            }
        }
        return $customer;
		}
	}
    /**
     * Save the customer information for existing customer with known email
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return $this
     */
    private function saveExistingCustomer($customer)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quote = $this->getQuote();
        //merge customer data from billing and shipping address to quote customer
        $customer = $this->mergeCustomerData($customer, $quote);
        try {
            $customer = $this->customerRepository->save($customer);
        } catch (\Magento\Framework\Validator\Exception $exception) {
            //this can happen when the existing customer data doesn't meet the address data requirements
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Error while saving the customer, please login to update your address data.')
            );
        }
        $quote->setCustomer($customer);
        return $this;
		}
	}
    /**
     * Save the customer information for existing customer
     *
     * @return void
     */
    private function saveCustomer()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			parent::_prepareCustomerQuote();
        $quote = $this->getQuote();
        $customer = $quote->getCustomer();
        $customer = $this->customerRepository->save($customer);
        $quote->setCustomer($customer);
		}
	}
    /**
     * Save as Guest
     *
     * @return void
     */
    private function saveAsGuest()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			parent::_prepareGuestQuote();
		}
	}
}
