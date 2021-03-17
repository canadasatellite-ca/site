<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Controller\Quote\Checkout;
/**
 * Trait DefaultCheckout
 *
 * @package Cart2Quote\Quotation\Controller\Quote\Checkout
 */
trait DefaultCheckout
{
    /**
     * Checks if auto login is allowed
     *
     * @return bool
     */
    private function isAutoLogin()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->helper->isAutoLoginEnabled();
		}
	}
    /**
     * Checks if auto confirm is allowed
     *
     * @return bool
     */
    private function isAutoConfirm()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->helper->isAutoConfirmProposalEnabled();
		}
	}
    /**
     * Proceed to checkout
     *
     * @param bool $guest
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    private function proceedToCheckout($guest = false)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->quote->getStatus() === \Cart2Quote\Quotation\Model\Quote\Status::STATUS_PROPOSAL_EXPIRED) {
            $url = $this->_url->getUrl('quotation/quote/view', ['quote_id' => $this->quote->getId()]);
            return $this->resultRedirectFactory->create()->setUrl($url);
        }
        $this->deletePreviousAcceptedQuotes();
        $this->initCheckoutQuote();
        $this->prepareQuotationQuote();
        $this->beforeSetCheckoutQuote();
        $this->saveCheckoutQuoteAsQuotationQuote();
        $this->quoteProposalAcceptedSender->send($this->quote);
        if ($guest) {
            $this->useGuestCheckout();
        }
        $this->processShipping();
        $this->deleteCurrentCheckoutSessionQuote();
        $this->placeCheckoutQuote();
        $this->helper->setActiveConfirmMode(true);
        return $this->redirectToCheckout();
		}
	}
    /**
     * Accept proposal
     *
     * @param bool $guest
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    private function proceedToAcceptQuotation($guest = false)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->prepareQuotationQuote();
        $this->quoteProposalAcceptedSender->send($this->quote);
        $quoteAcceptMessage = __('Thank you for accepting our offer. We will contact you shortly.');
        $this->messageManager->addSuccessMessage($quoteAcceptMessage);
        $url = $this->_url->getUrl('quotation/quote/view', ['quote_id' => $this->quote->getId()]);
        return $this->resultRedirectFactory->create()->setUrl($url);
		}
	}
    /**
     * Initialize the checkout quote
     *
     * @return $this
     */
    private function initCheckoutQuote()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!$this->checkoutQuote instanceof \Magento\Quote\Model\Quote) {
            $this->checkoutQuote = $this->mageQuoteFactory->create();
            $this->checkoutQuote->setLinkedQuotationId($this->quote->getId());
            //$this->checkoutQuote->setIsQuotationQuote(true); //Do not set this on a checkout quote
            $this->checkoutQuote->setStoreId($this->getStoreId());
            $this->checkoutQuote->save();
        }
        return $this;
		}
	}
    /**
     * Get store id
     *
     * @return int
     */
    private function getStoreId()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->_storeManager->getStore()->getId();
		}
	}
    /**
     * Prepare the quotation quote:
     *  Set state to complete
     *  Set status to accepted
     *  Set link to order
     *
     * @return $this
     */
    private function prepareQuotationQuote()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$customerId = $this->quote->getCustomerId();
        if ($customerId) {
            $quotes = $this->quoteCollectionFactory->create();
            $quotes->addFieldToFilter('customer_id', $customerId);
            $quotes->addFieldToFilter('is_active', 1);
            $quotes->addFieldToFilter('is_quotation_quote', 0);
            foreach ($quotes as $quote) {
                $quote->setIsActive(false)->save();
            }
        }
        $this->quote
            ->setState(\Cart2Quote\Quotation\Model\Quote\Status::STATE_COMPLETED)
            ->setStatus(\Cart2Quote\Quotation\Model\Quote\Status::STATUS_ACCEPTED)
            ->save();
        return $this;
		}
	}
    /**
     * Delete previously accepted quotes if they have same linked quotation id
     */
    private function deletePreviousAcceptedQuotes()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quotationId = $this->quote->getId();
        $customerId = $this->quote->getCustomerId();
        if ($customerId && $quotationId > 0) {
            $quotes = $this->quoteCollectionFactory->create();
            $quotes->addFieldToFilter('customer_id', $customerId);
            $quotes->addFieldToFilter('linked_quotation_id', $quotationId);
            foreach ($quotes as $quote) {
                if ($this->quote->getEntityId() !== $quote->getEntityId()) {
                    //remove linked quote id from this older quote
                    $quote->setLinkedQuotationId(null);
                    $this->quoteRepository->save($quote);
                    $this->quoteRepository->delete($quote);
                }
            }
        }
		}
	}
    /**
     * Prepare the checkout quote
     * - Further configuration of a new Quote and making it as a copy of approved & accepted C2Q_Quote object
     *
     * @return $this
     */
    private function beforeSetCheckoutQuote()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->checkoutQuote->setIsActive(true);
        $this->checkoutQuote->setBillingAddress($this->quote->getBillingAddress());
        $this->checkoutQuote->getBillingAddress()
            ->setId(null)
            ->setQuote($this->checkoutQuote);
        $this->checkoutQuote->setShippingAddress($this->quote->getShippingAddress());
        $this->checkoutQuote->getShippingAddress()
            ->setId(null)
            ->setQuote($this->checkoutQuote);
        $this->checkoutQuote->setCustomer($this->quote->getCustomer());
        $this->checkoutQuote->setTotalsCollectedFlag(false);
        return $this;
		}
	}
    /**
     * Prepare the checkout quote and save it as quotation quote.
     * - Transform a new Quote object into a copy of approved & accepted C2Q_Quote object
     *
     * @return $this
     */
    private function saveCheckoutQuoteAsQuotationQuote()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->checkoutQuote->merge($this->quote)->collectTotals();
        $this->quoteRepository->save($this->checkoutQuote);
        return $this;
		}
	}
    /**
     * Use the checkout quote as guest checkout
     *
     * @return $this
     */
    private function useGuestCheckout()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->checkoutQuote->setCheckoutMethod(\Magento\Checkout\Model\Type\Onepage::METHOD_GUEST);
        $this->processGuestCustomerData();
        return $this;
		}
	}
    /**
     * Process the customer data from the quotation quote to the checkout quote.
     * - Default copy functions do not copy this data.
     *
     * @return $this
     */
    private function processGuestCustomerData()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->quote->getData()) {
            foreach ($this->quote->getData() as $key => $value) {
                $keyExploded = explode('_', $key);
                if ($keyExploded[0] == 'customer') {
                    $this->checkoutQuote->setData($key, $value);
                }
            }
        }
        $this->checkoutQuote->save();
        return $this;
		}
	}
    /**
     * Process shipping
     *
     * @return void
     */
    private function processShipping()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->quote->getShippingAddress()->getShippingMethod() ==
            \Cart2Quote\Quotation\Model\Carrier\QuotationShipping::CODE . '_' .
            \Cart2Quote\Quotation\Model\Carrier\QuotationShipping::CODE
        ) {
            $baseFixedShippingPrice = $this->quote->convertShippingPrice($this->quote->getFixedShippingPrice(), true);
            $this->_quotationSession->addConfigData([
                $this->checkoutQuote->getId() => [
                    'fixed_shipping_price' => $baseFixedShippingPrice
                ]
            ]);
        }
		}
	}
    /**
     * Replace a current customer quote with it and remove the old one
     * - so customer will be able to place an Order with a new one
     *
     * @return $this
     */
    private function deleteCurrentCheckoutSessionQuote()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$oldCustomerQuote = $this->checkoutSession->getQuote();
        if ($oldCustomerQuote &&
            $oldCustomerQuote->getId() != $this->quote->getId() &&
            $oldCustomerQuote->getId() != $this->checkoutQuote->getId()
        ) {
            $oldCustomerQuote->setIsActive(false)->save();
            $this->quoteRepository->delete($oldCustomerQuote);
        }
        return $this;
		}
	}
    /**
     * Place the checkout quote in the checkout session
     *
     * @return $this
     */
    private function placeCheckoutQuote()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->checkoutSession->setQuoteId($this->checkoutQuote->getId());
        $this->checkoutSession->replaceQuote($this->checkoutQuote);
        $this->checkoutSession->setQuotationQuoteId($this->checkoutQuote->getId());
        return $this;
		}
	}
    /**
     * Redirect to checkout
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    private function redirectToCheckout()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->_scopeConfig->getValue(
            self::CONFIG_PATH_ENABLE_ALTERNATIVE_CHECKOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )) {
            return $this->alternativeCheckout();
        }
        return $this->resultRedirectFactory->create()->setPath('checkout');
		}
	}
    /**
     * Initialize the quotation quote
     *
     * @return $this
     */
    private function initQuote()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!$this->quote instanceof \Cart2Quote\Quotation\Model\Quote) {
            $quoteId = (int)$this->getRequest()->getParam('quote_id', false);
            $this->quote = $this->_quoteFactory->create()->load($quoteId);
        }
        return $this;
		}
	}
    /**
     * Check if the hash is valid
     *
     * @return bool
     */
    private function hasValidHash()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$validHash = false;
        $hash = $this->getRequest()->getParam('hash', false);
        if ($hash) {
            $validHash = $this->quote->getUrlHash() == $hash;
        }
        return $validHash;
		}
	}
    /**
     * Login by customer id set on the quote
     *
     * @return $this
     */
    private function autoLogin()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->quote->getCustomerId() > 0) {
            $this->customerSession->loginById($this->quote->getCustomerId());
        }
        return $this;
		}
	}
    /**
     * Redirect to quote page if the quote exists
     * - Redirect to index page if the quote does not exists
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    private function defaultRedirect()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->quote) {
            if ($this->quote->getStatus() !== \Cart2Quote\Quotation\Model\Quote\Status::STATUS_PROPOSAL_EXPIRED) {
                $this->quote->setStatus(\Cart2Quote\Quotation\Model\Quote\Status::STATUS_PENDING)->save();
            }
            $url = $this->_url->getUrl('quotation/quote/view', ['quote_id' => $this->quote->getId()]);
        } else {
            $url = $this->_url->getUrl('quotation/quote/index');
        }
        return $this->resultRedirectFactory->create()->setUrl($url);
		}
	}
    /**
     * Get the alyernative checkout path and set it to the redirect
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    private function alternativeCheckout()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$path = $this->_scopeConfig->getValue(
            self::CONFIG_PATH_ALTERNATIVE_CHECKOUT_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $this->resultRedirectFactory->create()->setPath($path);
		}
	}
    /**
     * Checks if a customer is a guest
     *
     * @return bool
     */
    private function isGuest()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return (bool)$this->quote->getCustomerIsGuest();
		}
	}
    /**
     * Checks if the customer is the same
     *
     * @return bool
     */
    private function isSameCustomer()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->quote->getCustomerId() == $this->cart->getCustomerSession()->getCustomerId();
		}
	}
}
