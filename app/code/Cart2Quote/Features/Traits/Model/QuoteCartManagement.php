<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;
/**
 * Trait QuoteCartManagement
 */
trait QuoteCartManagement
{
    /**
     * Returns information for the quote cart for a specified customer.
     *
     * @param int $customerId
     * @return \Cart2Quote\Quotation\Api\Data\QuoteCartInterface|\Magento\Quote\Api\Data\CartInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getQuoteCartForCustomer($customerId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->quotationRepository->getActiveForCustomer($customerId);
		}
	}
    /**
     * Creates an empty quote cart for a specified customer.
     *
     * @param int $customerId
     * @param int $storeId
     * @return int
     * @throws CouldNotSaveException
     */
    private function createEmptyQuoteCartForCustomer($customerId, $storeId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			try {
            $quote = $this->quotationRepository->getActiveForCustomer($customerId);
            $quote->setIsActive(false);
            $quote->save();
        } catch (NoSuchEntityException $e) {
            $quote = $this->createCustomerCart($customerId, $storeId);
            $this->_prepareCustomerQuote($quote);
            try {
                $this->quoteRepository->save($quote);
            } catch (\Exception $e) {
                throw new CouldNotSaveException(__("The quote can't be created."));
            }
            return (int)$quote->getId();
        }
		}
	}
    /**
     * Creates a cart for the currently logged-in customer.
     *
     * @param int $customerId
     * @param int $storeId
     * @return \Magento\Quote\Model\Quote Cart object.
     * @throws CouldNotSaveException The cart could not be created.
     */
    private function createCustomerCart($customerId, $storeId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			try {
            $sharedStoreId = [$storeId];
            $quote = $this->quoteRepository->getActiveForCustomer($customerId, $sharedStoreId);
            $quote->setIsQuotationQuote(1);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $customer = $this->customerRepository->getById($customerId);
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this->quoteFactory->create();
            $quote->setIsQuotationQuote(1);
            $quote->setStoreId($storeId);
            $quote->setCustomer($customer);
            $quote->setCustomerIsGuest(0);
        }
        return $quote;
		}
	}
    /**
     * Prepare address for customer quote.
     *
     * @param Quote $quote
     * @return void
     */
    private function _prepareCustomerQuote($quote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			/** @var Quote $quote */
        $billing = $quote->getBillingAddress();
        $shipping = $quote->getShippingAddress();
        if ($quote->isVirtual()) {
            //don't set a shipping address on a quote if there are only virtual products
            $shipping = null;
        }
        $customer = $this->customerRepository->getById($quote->getCustomerId());
        $this->prepareShippingAddress($quote, $customer, $shipping, $billing);
        $this->prepareBillingAddress($quote, $customer, $billing);
		}
	}
    /**
     * Prepare shipping address for customer quote
     *
     * @param $quote
     * @param $customer
     * @param $shipping
     * @param $billing
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function prepareShippingAddress($quote, $customer, $shipping, $billing)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$hasDefaultBilling = (bool)$customer->getDefaultBilling();
        $hasDefaultShipping = (bool)$customer->getDefaultShipping();
        if ($shipping && !$shipping->getSameAsBilling()
            && (!$shipping->getCustomerId() || $shipping->getSaveInAddressBook())
        ) {
            if ($shipping->getQuoteId()) {
                $shippingAddress = $shipping->exportCustomerAddress();
            } else {
                $defaultShipping = $this->customerRepository->getById($customer->getId())->getDefaultShipping();
                if ($defaultShipping) {
                    try {
                        $shippingAddress = $this->addressRepository->getById($defaultShipping);
                    } catch (LocalizedException $e) {
                        // no address
                    }
                }
            }
            if (isset($shippingAddress)) {
                if (!$hasDefaultShipping) {
                    //Make provided address as default shipping address
                    $shippingAddress->setIsDefaultShipping(true);
                    $hasDefaultShipping = true;
                    if (!$hasDefaultBilling && !$billing->getSaveInAddressBook()) {
                        $shippingAddress->setIsDefaultBilling(true);
                        $hasDefaultBilling = true;
                    }
                }
                //save here new customer address
                $shippingAddress->setCustomerId($quote->getCustomerId());
                $this->addressRepository->save($shippingAddress);
                $quote->addCustomerAddress($shippingAddress);
                $shipping->setCustomerAddressData($shippingAddress);
                $this->addressesToSync[] = $shippingAddress->getId();
                $shipping->setCustomerAddressId($shippingAddress->getId());
            }
        }
        if ($shipping && !$shipping->getCustomerId() && !$hasDefaultBilling) {
            $shipping->setIsDefaultBilling(true);
        }
		}
	}
    /**
     * Prepare billing address for customer quote
     *
     * @param $quote
     * @param $customer
     * @param $billing
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function prepareBillingAddress($quote, $customer, $billing)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$hasDefaultBilling = (bool)$customer->getDefaultBilling();
        $hasDefaultShipping = (bool)$customer->getDefaultShipping();
        if (!$billing->getCustomerId() || $billing->getSaveInAddressBook()) {
            if ($billing->getQuoteId()) {
                $billingAddress = $billing->exportCustomerAddress();
            } else {
                $defaultBilling = $this->customerRepository->getById($customer->getId())->getDefaultBilling();
                if ($defaultBilling) {
                    try {
                        $billingAddress = $this->addressRepository->getById($defaultBilling);
                    } catch (LocalizedException $e) {
                        // no address
                    }
                }
            }
            if (isset($billingAddress)) {
                if (!$hasDefaultBilling) {
                    //Make provided address as default shipping address
                    if (!$hasDefaultShipping) {
                        //Make provided address as default shipping address
                        $billingAddress->setIsDefaultShipping(true);
                    }
                    $billingAddress->setIsDefaultBilling(true);
                }
                $billingAddress->setCustomerId($quote->getCustomerId());
                $this->addressRepository->save($billingAddress);
                $quote->addCustomerAddress($billingAddress);
                $billing->setCustomerAddressData($billingAddress);
                $this->addressesToSync[] = $billingAddress->getId();
                $billing->setCustomerAddressId($billingAddress->getId());
            }
        }
		}
	}
    /**
     * Request Quote
     *
     * @param int $customerId
     * @return string response
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function requestQuote($customerId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quote = $this->quotationRepository->getActiveForCustomer($customerId);
        $quote->setIsActive(false);
        $quote->save();
        $this->_prepareCustomerQuote($quote);
        $quoteModel = $this->quotationFactory->create();
        $quotation = $quoteModel->create($quote)->load($quote->getId());
        $quotation->saveQuote();
        $this->sender->send($quotation);
        $this->quoteSession->fullSessionClear();
        $this->quoteSession->updateLastQuote($quotation);
        $response = __('Quote ' . $quotation->getIncrementId() . ' created');
        return $response;
		}
	}
}
