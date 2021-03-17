<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Controller\Quote\Ajax;
use Cart2Quote\Quotation\Model\QuotationCart as CustomerCart;
/**
 * Trait CreateQuote
 *
 * @package Cart2Quote\Quotation\Controller\Quote\Ajax
 */
trait CreateQuote
{
    /**
     * Request customer's quote.
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function processAction()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quote = $this->getOnepage()->getQuote();
        $this->addQuotationData();
        $phoneRequest = $this->isPhoneRequest($quote);
        $this->saveCustomer($quote);
        $quote->assignCustomerWithAddressChange(
            $quote->getCustomer(),
            $this->addressHelper->getEnableForm() ? $quote->getBillingAddress() : null,
            $this->addressHelper->getEnableForm() ? $quote->getShippingAddress() : null
        );
        //save some data for later checks and fallbacks
        $prefixName = $quote->getCustomerPrefix();
        $firstName = $quote->getCustomerFirstname();
        $middleName = $quote->getCustomerMiddlename();
        $lastName = $quote->getCustomerLastname();
        $suffixName = $quote->getCustomerSuffix();
        $dob = $quote->getCustomerDob();
        $gender = $quote->getCustomerGender();
        $email = $quote->getData('customer_email');
        //this function calls a save action on the quote cart that can have module conflicts, so we try to log that
        $this->updateQuotationProductData();
        //check if we lost the email address
        if ($quote->getData('customer_email') != $email) {
            //WARNING; There is very likely a module conflict on the checkout_cart_save_before or checkout_cart_save_after events. Please fix that or contact us.
            //$this->logger->info('C2Q: Probable module conflict');
            $quote->setCustomerPrefix($prefixName);
            $quote->setCustomerFirstname($firstName);
            $quote->setCustomerMiddlename($middleName);
            $quote->setCustomerLastname($lastName);
            $quote->setCustomerSuffix($suffixName);
            $quote->setCustomerDob($dob);
            $quote->setCustomerGender($gender);
            $quote->setCustomerEmail($email);
        }
        if (!$this->addressHelper->getDisplayShipping()) {
            $this->removeForcedShipping($quote);
        }
        if ($this->getRequest()->getParam('clear_quote', false)) {
            $quote->setIsActive(false);
        }
        $quotation = $this->save($quote);
        //Duplicate quote for Frontend Quote Changes Visibility feature
        if ($this->helper->quoteChangesVisibility()) {
            $this->cloningHelper->createOriginalQuote($quotation);
        }
        if (is_array($this->fileModel->getFileDataFromSession())) {
            $this->fileModel->saveFileQuotationQuote($quotation);
        }
        if (!$phoneRequest) {
            $this->sendEmailToCustomer($quotation);
        }
        if ($this->getRequest()->getParam('clear_quote', false)) {
            $this->quoteSession->fullSessionClear();
            $this->quoteSession->updateLastQuote($quotation);
        }
        $this->result->setData('last_quote_id', $quotation->getId());
        $this->_eventManager->dispatch(
            'quotation_event_after_quote_request',
            ['quote' => $quotation]
        );
        return true;
		}
	}
    /**
     * Save the customer.
     *
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    private function saveCustomer(\Magento\Quote\Model\Quote $quote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->isCustomerLoggedIn()) {
            $this->getOnepage()->setQuote($quote)->saveCustomer();
            return;
        }
        $customerEmail = $this->getRequest()->getParam('customer_email', false);
        //add support for + emails
        $customerEmail = str_replace(" ", "+", trim($customerEmail));
        $checkoutAsGuest = filter_var(
            $this->getRequest()->getParam('checkout_as_guest', false),
            FILTER_VALIDATE_BOOLEAN
        );
        $customer = $this->validateCustomerEmail($customerEmail);
        if ($customerEmail && $customerEmail != 'null') {
            $quote = $this->setCustomerName($quote);
            $quote->setCustomerEmail($customerEmail);
            if ($checkoutAsGuest) {
                $this->getOnepage()->setQuote($quote)->saveAsGuest();
            } else {
                if ($customer) {
                    $this->getOnepage()->setQuote($quote)->saveExistingCustomer($customer);
                } else {
                    $this->getOnepage()->setQuote($quote)->saveNewCustomer();
                    if ($this->addressHelper->getAutoLogIn()) {
                        $this->quoteSession->setSkipLoadCustomer(true);
                        $this->autoLogin($quote);
                    }
                }
            }
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Email address is mandatory for a quote.')
            );
        }
		}
	}
    /**
     * Check if customer exists
     *
     * @param string $email
     * @return bool|\Magento\Customer\Api\Data\CustomerInterface
     */
    private function validateCustomerEmail($email)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			try {
            $customer = $this->customerRepository->get($email);
            return $customer;
        } catch (\Magento\Framework\Exception\LocalizedException  $e) {
            // If the customer does not exists a localizedException will be thrown.
            return false;
        }
		}
	}
    /**
     * Set the first and last name
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return \Magento\Quote\Model\Quote
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function setCustomerName(\Magento\Quote\Model\Quote $quote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			/** Get from billing address */
        $firstName = $quote->getBillingAddress()->getFirstname();
        $lastName = $quote->getBillingAddress()->getLastname();
        /** Get from shipping address */
        if (!$firstName && !$lastName) {
            $firstName = $quote->getShippingAddress()->getFirstname();
            $lastName = $quote->getShippingAddress()->getLastname();
        }
        /** Get from quotation session */
        if (!$firstName && !$lastName) {
            $quoteData = $this->quoteSession->getData(
                \Cart2Quote\Quotation\Model\Session::QUOTATION_GUEST_FIELD_DATA
            );
            if (isset($quoteData['firstname'], $quoteData['lastname'])) {
                $firstName = $quoteData['firstname'];
                $lastName = $quoteData['lastname'];
            }
        }
        if ($firstName && $lastName) {
            $quote->setCustomerFirstname($firstName);
            $quote->setCustomerLastname($lastName);
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('First and Last name are mandatory for a quote.')
            );
        }
        return $quote;
		}
	}
    /**
     * Auto login the customer
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    private function autoLogin(\Magento\Quote\Model\Quote $quote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($customer = $quote->getCustomer()) {
            if (!$customer->getId()) {
                $quote->setCustomer($this->customerRepository->save($customer));
            }
            $this->_customerSession->setCustomerDataAsLoggedIn($quote->getCustomer());
        }
		}
	}
    /**
     * Update the fields from the quotation data on the session.
     *
     * @return void
     */
    private function addQuotationData()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quoteData = $this->quoteSession->getData(
            \Cart2Quote\Quotation\Model\Session::QUOTATION_FIELD_DATA
        );
        $this->updateCustomerNote($quoteData);
        $this->updateCustomerGender($quoteData);
        $this->updateCustomerDob($quoteData);
		}
	}
    /**
     * Update that customer note on the quote.
     *
     * @param array $quoteData
     *
     * @return void
     */
    private function updateCustomerNote($quoteData)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (isset($quoteData[\Cart2Quote\Quotation\Model\Quote::KEY_CUSTOMER_NOTE])) {
            $this->quotationCart->getQuote()->setCustomerNote(
                strip_tags($quoteData[\Cart2Quote\Quotation\Model\Quote::KEY_CUSTOMER_NOTE])
            );
        }
		}
	}
    /**
     * Update that customer gender on the quote.
     *
     * @param array $quoteData
     *
     * @return void
     */
    private function updateCustomerGender($quoteData)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (isset($quoteData['gender'])) {
            $this->quotationCart->getQuote()->setCustomerGender(
                strip_tags($quoteData['gender'])
            );
        }
		}
	}
    /**
     * Update that customer dob on the quote.
     *
     * @param array $quoteData
     *
     * @return void
     */
    private function updateCustomerDob($quoteData)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (isset($quoteData['dob'])) {
            //fix for Invalid date (when no dob field is present)
            if ($quoteData['dob'] == 'Invalid date') {
                unset($quoteData['dob']);
                $this->quotationCart->getQuote()->setCustomerDob(null);
                return;
            }
            $this->quotationCart->getQuote()->setCustomerDob(
                strip_tags($quoteData['dob'])
            );
        }
		}
	}
    /**
     * Save the Quotation Quote.
     *
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    private function save(\Magento\Quote\Model\Quote $quote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quoteModel = $this->quoteFactory->create();
        $quotation = $quoteModel->create($quote)->load($quote->getId());
        return $quotation;
		}
	}
    /**
     * Send the quote email to the customer.
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quotation
     *
     * @return void
     */
    private function sendEmailToCustomer(\Cart2Quote\Quotation\Model\Quote $quotation)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->sender->send($quotation);
		}
	}
    /**
     * Remove shipping from quote
     *
     * @param \Magento\Quote\Model\Quote $quote
     */
    private function removeForcedShipping($quote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quote->getShippingAddress()->setShippingMethod(null);
        $quote->getShippingAddress()->setShippingDescription(null);
		}
	}
    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return bool
     */
    private function isPhoneRequest($quote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quoteData = $this->quoteSession->getData(
            \Cart2Quote\Quotation\Model\Session::QUOTATION_GUEST_FIELD_DATA
        );
        if (is_array($quoteData)) {
            if (array_key_exists('guest_telephone', $quoteData)) {
                $phoneNumber = $quoteData['guest_telephone'];
                if (isset($phoneNumber) && !empty($phoneNumber)) {
                    $quote->setIsPhoneOnly(true);
                    $quote->getBillingAddress()->setTelephone($phoneNumber);
                    $quote->getBillingAddress()->setEmail("telephone@example.com");
                    return true;
                }
            }
        }
        return false;
		}
	}
}
