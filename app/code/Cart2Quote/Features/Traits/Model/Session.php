<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model;
/**
 * Trait Session
 *
 * @package Cart2Quote\Quotation\Model
 */
trait Session
{
    /**
     * Load data for customer quote and merge with current quote
     *
     * @return $this
     */
    private function loadCustomerQuote()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!$this->_customerSession->getCustomerId() || $this->skipLoadCustomerQuote) {
            return $this;
        }
        $this->setQuotationQuote(true);
        $this->_eventManager->dispatch('load_customer_quote_before', ['quotation_session' => $this]);
        try {
            /** @var Quote $quote */
            $quote = $this->quoteFactory->create();
            $quote->setStoreId($this->_storeManager->getStore()->getId());
            $customerQuote = $this->quoteResourceModel->loadByCustomerId(
                $quote,
                $this->_customerSession->getCustomerId()
            );
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $customerQuote = $this->quoteFactory->create();
        }
        if (!isset($customerQuote)) {
            $customerQuote = $this->quoteFactory->create();
        }
        $customerQuote->setStoreId($this->_storeManager->getStore()->getId());
        if ($customerQuote->getId() && $this->getQuoteId() != $customerQuote->getId()) {
            if ($this->getQuoteId()) {
                $this->quoteRepository->save(
                    $customerQuote->merge($this->getQuote())->collectTotals()
                );
            }
            $this->setQuoteId($customerQuote->getId());
            if ($this->_quote) {
                $this->quoteRepository->delete($this->_quote);
            }
            $this->_quote = $customerQuote;
        } else {
            $this->getQuote()->getBillingAddress();
            $this->getQuote()->getShippingAddress();
            $this->getQuote()->setCustomer($this->_customerSession->getCustomerDataObject())
                ->setTotalsCollectedFlag(false)
                ->collectTotals();
            $this->getQuote()->setIsQuotationQuote(true);
            $this->quoteRepository->save($this->getQuote());
            $this->setQuoteId($this->getQuote()->getId());
        }
        $this->setQuotationQuote(false);
        return $this;
		}
	}
    /**
     * Get quotation quote instance by current session
     *
     * @return Quote
     */
    private function getQuote()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_eventManager->dispatch('quotation_quote_process', ['quotation_session' => $this]);
        if ($this->_quote === null) {
            $quote = $this->quoteFactory->create();
            if ($this->getQuoteId()) {
                try {
                    $quote = $this->quoteRepository->get($this->getQuoteId());
                    /**
                     * If current currency code of quote is not equal current currency code of store,
                     * need recalculate totals of quote. It is possible if customer use currency switcher or
                     * store switcher.
                     */
                    if ($quote->getQuoteCurrencyCode() != $this->_storeManager->getStore()->getCurrentCurrencyCode()) {
                        $quote->setStore($this->_storeManager->getStore());
                        $this->quoteRepository->save($quote->collectTotals());
                        /*
                         * We mast to create new quote object, because collectTotals()
                         * can to create links with other objects.
                         */
                        $quote = $this->quoteRepository->get($this->getQuoteId());
                    }
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $this->setQuoteId(null);
                }
            }
            if (!$this->getQuoteId()) {
                if ($this->_customerSession->isLoggedIn() || $this->_customer) {
                    $this->setQuoteId($quote->getId());
                } else {
                    $quote->setIsQuotationCart(true);
                    $this->_eventManager->dispatch('quotation_quote_init', ['quote' => $quote]);
                }
            }
            if ($this->_customer) {
                $quote->setCustomer($this->_customer);
            } elseif ($this->_customerSession->isLoggedIn()) {
                $quote->setCustomer($this->customerRepository->getById($this->_customerSession->getCustomerId()));
            }
            $quote->setStore($this->_storeManager->getStore());
            $this->_quote = $quote;
        }
        if (!$this->getIsQuoteMasked() && !$this->_customerSession->isLoggedIn() && $this->getQuoteId()) {
            $quoteId = $this->getQuoteId();
            /** @var \Magento\Quote\Model\QuoteIdMask $quoteIdMask */
            $quoteIdMask = $this->quoteIdMaskFactory->create()->load($quoteId, 'quote_id');
            if ($quoteIdMask->getMaskedId() === null) {
                $quoteIdMask->setQuoteId($quoteId)->save();
            }
            $this->setIsQuoteMasked(true);
        }
        $remoteAddress = $this->_remoteAddress->getRemoteAddress();
        if ($remoteAddress) {
            $this->_quote->setRemoteIp($remoteAddress);
            $xForwardIp = $this->request->getServer('HTTP_X_FORWARDED_FOR');
            $this->_quote->setXForwardedFor($xForwardIp);
        }
        return $this->_quote;
		}
	}
    /**
     * Load item comments from the database and store it to the session
     */
    private function loadProductComments()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$description = [];
        $quote = $this->getQuote();
        if (isset($quote)) {
            $items = $quote->getItemsCollection();
            foreach ($items as $item) {
                $itemDescription = $item->getDescription();
                if (isset($itemDescription)) {
                    $description[$item->getItemId()] = $item->getDescription();
                }
            }
            if (!empty($description)) {
                $config['description'] = $description;
                $this->addProductData($config);
            }
        }
		}
	}
    /**
     * Clear the Quotation Quote from the session.
     *
     * @return $this
     */
    private function fullSessionClear()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->clearQuote()->clearStorage();
        return $this;
		}
	}
    /**
     * Destroy/end a session
     * -Unset all data associated with object
     *
     * @return $this
     */
    private function clearQuote()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_eventManager->dispatch('quotation_quote_destroy', ['quote' => $this->getQuote()]);
        $this->_quote = null;
        $this->setQuoteId(null);
        return $this;
		}
	}
    /**
     * Update the quote ID's and status on the session
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quotation
     * @return $this
     */
    private function updateLastQuote(\Cart2Quote\Quotation\Model\Quote $quotation)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setLastQuoteId($quotation->getId())
            ->setLastRealQuoteId($quotation->getIncrementId())
            ->setLastQuoteStatus($quotation->getStatus())
            ->setClonedQuoteId($quotation->getClonedQuoteId());
        return $this;
		}
	}
    /**
     * Add config to the session
     * - Note: This data will be available on the RFQ page in as JSON data
     *
     * @param array $config
     * @return $this
     */
    private function addGuestFieldData(array $config)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->addMergeData(self::QUOTATION_GUEST_FIELD_DATA, $config);
        return $this;
		}
	}
    /**
     * Merge Data
     *
     * @param string $type
     * @param array $newConfig
     * @return $this
     */
    private function addMergeData($type, $newConfig)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$initialData = $this->getData($type);
        if (is_array($initialData)) {
            $newConfig = $newConfig + $initialData;
        }
        $this->storage->setData($type, $newConfig);
        return $this;
		}
	}
    /**
     * Add config to the session
     * - Note: This data will be available on the RFQ page in as JSON data
     *
     * @param array $config
     * @return $this
     */
    private function addConfigData(array $config)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->addMergeData(self::QUOTATION_STORE_CONFIG_DATA, $config);
        return $this;
		}
	}
    /**
     * Add product data to the session
     * - Note: This data will be available on the RFQ page in as JSON data
     *
     * @param array $config
     * @return $this
     */
    private function addProductData(array $config)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->addMergeData(self::QUOTATION_PRODUCT_DATA, $config);
        return $this;
		}
	}
    /**
     * Add field data to the session
     * - Note: This data will be available on the RFQ page in as JSON data
     *
     * @param array $config
     * @return $this
     */
    private function addFieldData(array $config)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->addMergeData(self::QUOTATION_FIELD_DATA, $config);
        return $this;
		}
	}
    /**
     * Set quotation quote to quotation session
     *
     * @param boolean $isQuotationQuote
     */
    private function setQuotationQuote($isQuotationQuote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData("quotation_quote", $isQuotationQuote);
		}
	}
    /**
     * Get quotation quote from quotation session
     *
     * @return boolean|null
     */
    private function getQuotationQuote()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData("quotation_quote");
		}
	}
    /**
     * Set skip load customer
     *
     * @param boolean $skip
     */
    private function setSkipLoadCustomer($skip)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->skipLoadCustomerQuote = $skip;
		}
	}
}
