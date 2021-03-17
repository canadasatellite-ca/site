<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Controller\Adminhtml;
/**
 * Adminhtml quotation quotes controller
 */
trait Quote
{
    /**
     * Init layout, menu and breadcrumb
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    private function _initAction()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Cart2Quote_Quotation::quotation_quote');
        $resultPage->addBreadcrumb(__('Quotation'), __('Quotation'));
        $resultPage->addBreadcrumb(__('Quotes'), __('Quotes'));
        return $resultPage;
		}
	}
    /**
     * Initialize quote model instance
     *
     * @return \Cart2Quote\Quotation\Model\Quote|false
     */
    private function _initQuote()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$id = $this->getRequest()->getParam('quote_id');
        if (!$id) {
            $id = $this->_getSession()->getQuotationQuoteId();
        }
        $this->_currentQuote = $this->quoteFactory->create()->load($id);
        if (!$this->_currentQuote->getId()) {
            $this->messageManager->addError(__('This quote no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        $this->_coreRegistry->unregister('current_quote');
        $this->_coreRegistry->register('current_quote', $this->_currentQuote);
        return $this->_currentQuote;
		}
	}
    /**
     * Retrieve session object
     *
     * @return \Magento\Backend\Model\Session\Quote
     */
    private function _getSession()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->backendQuoteSession;
		}
	}
    /**
     * Acl check for admin
     *
     * @return bool
     */
    private function _isAllowed()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->_authorization->isAllowed('Cart2Quote_Quotation::quotes');
		}
	}
    /**
     * Quotes grid
     *
     * @return null|\Magento\Backend\Model\View\Result\Page
     */
    private function execute()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return null;
		}
	}
    /**
     * Initialize quote creation session data
     *
     * @return $this
     */
    private function _initSession()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			/**
         * Identify quote
         */
        if ($quoteId = $this->getRequest()->getParam('quote_id')) {
            $this->_getSession()->setQuotationQuoteId((int)$quoteId);
        } else {
            if ($quote = $this->getCurrentQuote()) {
                $this->_getSession()->setQuotationQuoteId((int)$quote->getId());
            }
        }
        /**
         * Identify customer
         */
        $this->_getSession()->setCustomerId(null);
        if ($customerId = $this->getRequest()->getParam('customer_id')) {
            $this->_getSession()->setCustomerId((int)$customerId);
        } else {
            if ($quote = $this->getCurrentQuote()) {
                if ($customerId = $quote->getCustomerId()) {
                    $this->_getSession()->setCustomerId((int)$customerId);
                }
            }
        }
        /**
         * Identify store
         */
        if ($storeId = $this->getRequest()->getParam('store_id')) {
            $this->_getSession()->setStoreId((int)$storeId);
        } else {
            if ($quote = $this->getCurrentQuote()) {
                if ($storeId = $quote->getStoreId()) {
                    $this->_getSession()->setStoreId((int)$storeId);
                }
            }
        }
        /**
         * Identify currency
         */
        if ($currencyId = $this->getRequest()->getParam('currency_id')) {
            $this->_getSession()->setCurrencyId((string)$currencyId);
            $this->getCurrentQuote()->setRecollect(true);
        } else {
            if ($quote = $this->getCurrentQuote()) {
                if ($currencyId = $quote->getQuoteCurrencyCode()) {
                    $this->_getSession()->setCurrencyId((string)$currencyId);
                    $this->store->setCurrentCurrencyCode($currencyId);
                    $this->getCurrentQuote()->setRecollect(true);
                }
            }
        }
        return $this;
		}
	}
    /**
     * Retrieve quote create model
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    private function getCurrentQuote()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!isset($this->_currentQuote)) {
            if ($this->_coreRegistry->registry('current_quote')) {
                return $this->_currentQuote = $this->_coreRegistry->registry('current_quote');
            }
            //if quote isn't set, return new quote model
            return $this->_currentQuote = $this->quoteFactory->create();
        }
        return $this->_currentQuote;
		}
	}
    /**
     * Processing request data
     *
     * @return $this
     */
    private function _processData()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->_processActionData();
		}
	}
    /**
     * Process request data with additional logic for saving quote and creating order
     *
     * @param string $action
     *
     * @return $this
     */
    private function _processActionData($action = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$eventData = [
            'quote_model' => $this->getCurrentQuote(),
            'request_model' => $this->getRequest(),
            'session' => $this->_getSession(),
        ];
        $this->_eventManager->dispatch('adminhtml_quotation_quote_view_process_data_before', $eventData);
        $data = $this->getRequest()->getPost('quote');
        /**
         * Saving order data
         */
        if ($data) {
            $this->getCurrentQuote()->importPostData($data);
            $quote = $this->getRequest()->getParam('quote', false);
            if (!isset($data['expiry_enabled'])) {
                $this->getCurrentQuote()->setExpiryEnabled(false);
            }
            if (!isset($data['reminder_enabled'])) {
                $this->getCurrentQuote()->setReminderEnabled(false);
            }
            if (isset($quote['status'])) {
                $newStatus = $quote['status'];
                $status = $this->_statusCollection->getItemByColumnValue('status', $newStatus);
                $state = $status->getState();
                $this->getCurrentQuote()->setState($state);
            }
        }
        /**
         * Set ignore stock check
         */
        if (!$this->helperData->isStockEnabledBackend()) {
            $this->getCurrentQuote()->setIsSuperMode(true);
        }
        /**
         * Prevent setting null quantity on stock check
         */
        $this->getCurrentQuote()->setIgnoreOldQty(true);
        /**
         * Set correct currency
         */
        $this->processCurrency();
        /**
         * Initialize catalog rule data
         */
        $this->getCurrentQuote()->initRuleData();
        /**
         * Process addresses
         */
        $this->_processAddresses();
        /**
         * Process shipping
         */
        $this->_processShipping();
        /**
         * Adding product to quote from shopping cart, wishlist etc.
         */
        if ($productId = (int)$this->getRequest()->getPost('add_product')) {
            $this->getCurrentQuote()->addProduct($productId, $this->getRequest()->getPostValue());
        }
        /**
         * Adding products to quote from special grid
         */
        if ($this->getRequest()->has('item') && !$this->getRequest()->getPost('update_items') && !($action == 'save')) {
            $items = $this->getRequest()->getPost('item');
            $items = $this->_processFiles($items);
            $this->getCurrentQuote()->addProducts($items);
        }
        /**
         * Set Subtotal Proposal
         */
        $this->_setSubtotalProposal();
        /**
         * Update quote items
         */
        $this->_updateQuoteItems();
        /**
         * Remove quote item
         */
        $this->_removeQuoteItem();
        $this->getCurrentQuote()->updateBaseCustomPrice();
        /**
         * Save payment data
         */
        if ($paymentData = $this->getRequest()->getPost('payment')) {
            $this->getCurrentQuote()->getPayment()->addData($paymentData);
        }
        /**
         * Process gift message
         */
        $this->_processGiftMessage();
        $couponCode = '';
        if (isset($data) && isset($data['coupon']['code'])) {
            $couponCode = trim($data['coupon']['code']);
        }
        if (!empty($couponCode)) {
            $isApplyDiscount = false;
            foreach ($this->getCurrentQuote()->getAllItems() as $item) {
                if (!$item->getNoDiscount()) {
                    $isApplyDiscount = true;
                    break;
                }
            }
            if (!$isApplyDiscount) {
                $this->messageManager->addError(
                    __(
                        '"%1" coupon code was not applied. Do not apply discount is selected for item(s)',
                        $this->escaper->escapeHtml($couponCode)
                    )
                );
            } else {
                if ($this->getCurrentQuote()->getCouponCode() !== $couponCode) {
                    $this->messageManager->addError(
                        __(
                            '"%1" coupon code is not valid.',
                            $this->escaper->escapeHtml($couponCode)
                        )
                    );
                } else {
                    $this->messageManager->addSuccess(__('The coupon code has been accepted.'));
                }
            }
        }
        $eventData = [
            'quote_model' => $this->getCurrentQuote(),
            'request' => $this->getRequest()->getPostValue(),
        ];
        $this->_eventManager->dispatch('adminhtml_quotation_quote_view_process_data', $eventData);
        $this->getCurrentQuote()->saveQuote();
        return $this;
		}
	}
    /**
     * Function Process the quote addresses
     */
    private function _processAddresses()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			/**
         * init first billing address, need for virtual products
         */
        $this->getCurrentQuote()->getBillingAddress();
        /**
         * Flag for using billing address for shipping
         */
        if (!$this->getCurrentQuote()->isVirtual()) {
            $syncFlag = $this->getRequest()->getPost('shipping_as_billing');
            $shippingMethod = $this->getCurrentQuote()->getShippingAddress()->getShippingMethod();
            if ($syncFlag === null
                && $this->getCurrentQuote()->getShippingAddress()->getSameAsBilling() && empty($shippingMethod)
            ) {
                $this->getCurrentQuote()->setShippingAsBilling(1);
            } else {
                $this->getCurrentQuote()->setShippingAsBilling((int)$syncFlag);
            }
        }
		}
	}
    /**
     * Function Process the quote shipping method
     */
    private function _processShipping()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			/**
         * Change shipping address flag
         */
        if (!$this->getCurrentQuote()->isVirtual() && $this->getRequest()->getPost('reset_shipping')) {
            $this->getCurrentQuote()->resetShippingMethod();
        }
        /**
         * Collecting shipping rates
         */
        if (!$this->getCurrentQuote()->isVirtual() && $this->getRequest()->getPost('collect_shipping_rates')) {
            $this->getCurrentQuote()->save();
            $this->getCurrentQuote()->collectShippingRates();
        }
		}
	}
    /**
     * Process buyRequest file options of items
     *
     * @param array $items
     * @return array
     */
    private function _processFiles($items)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			foreach ($items as $id => $item) {
            $buyRequest = new \Magento\Framework\DataObject($item);
            $params = ['files_prefix' => 'item_' . $id . '_'];
            $buyRequest = $this->productHelper->addParamsToBuyRequest($buyRequest, $params);
            if ($buyRequest->hasData()) {
                $items[$id] = $buyRequest->toArray();
            }
        }
        return $items;
		}
	}
    /**
     * Update the quote items based on the data provided in the post data
     *
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function _updateQuoteItems()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->getRequest()->getPost('update_items')) {
            $this->quoteCreate->setQuote($this->getCurrentQuote());
            $items = $this->getRequest()->getPost('item', []);
            $items = $this->_processFiles($items);
            $items = $this->quoteCreate->updateQuotationItems($items);
            $this->quoteCreate->updateTierItems($items);
            if ($this->getRequest()->getPost('remove_items')) {
                foreach ($items as $key => $item) {
                    if ($item['action'] == 'remove') {
                        $this->getCurrentQuote()->removeQuotationItem($key);
                    }
                }
            }
            if ($this->getRequest()->getPost('duplicate_items')) {
                foreach ($items as $key => $item) {
                    if ($item['action'] == 'duplicate') {
                        $originalItem = $this->getCurrentQuote()->getItemById($key);
                        if (!$originalItem->getId()) {
                            throw new \Magento\Framework\Exception\NoSuchEntityException(
                                __('Item %1 does not exist', $key)
                            );
                        }
                        $clonedItems = $this->cloningHelper->cloneItem($originalItem);
                        foreach ($clonedItems as $clonedItem) {
                            $this->getCurrentQuote()->getItemsCollection()->addItem(
                                $clonedItem
                            );
                        }
                    }
                }
            }
        }
		}
	}
    /**
     * Check if negative profit is disabled
     *
     * @return mixed
     */
    private function isDisabledNegativeProfit()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->scopeConfig->getValue(
            'cart2quote_advanced/negativeprofit/disable_negative_profit',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		}
	}
    /**
     * Set the currency, collected from the post data, on the quote.
     *
     * @return $this;
     */
    private function processCurrency()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$currency = $this->getRequest()->getPost('currency_id');
        if ($currency) {
            $oldCurrency = $this->getCurrentQuote()->getQuoteCurrency()->getCode();
            if ($currency != $oldCurrency) {
                if ($currency == "false") {
                    $currency = $this->getCurrentQuote()->getBaseCurrency()->getCode();
                    $this->getCurrentQuote()->setQuoteCurrencyCode($currency);
                } else {
                    $this->getCurrentQuote()->setQuoteCurrencyCode($currency);
                }
                $rate = $this->getCurrentQuote()->getAnyCurrency($oldCurrency)->getAnyRate($currency);
                $this->getCurrentQuote()->setBaseToQuoteRate(
                    $this->getCurrentQuote()->getBaseCurrency()->getRate($currency)
                );
                $this->getCurrentQuote()->setStoreToQuoteRate($rate);
                $this->getCurrentQuote()->resetQuoteCurrency();
            }
        }
        return $this;
		}
	}
    /**
     * Remove a quote item based on the post data
     */
    private function _removeQuoteItem()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$removeItemId = (int)$this->getRequest()->getPost('remove_item');
        $removeFrom = (string)$this->getRequest()->getPost('from');
        if ($removeItemId && $removeFrom) {
            $this->getCurrentQuote()->removeQuotationItem($removeItemId);
        }
		}
	}
    /**
     * Sets the proposal subtotal
     */
    private function _setSubtotalProposal()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$proposal = $this->getRequest()->getPost('proposal');
        if (isset($proposal) && isset($proposal['subtotal_proposal'])) {
            if (isset($proposal['proposal_is_percentage']) && $proposal['proposal_is_percentage'] === 'true') {
                $isPercentage = true;
            } else {
                $isPercentage = false;
            }
            $amount = (float)$proposal['subtotal_proposal'];
            $this->getCurrentQuote()->setSubtotalProposal($amount, $isPercentage);
        }
		}
	}
    /**
     * Trigers the giftmessage methods
     *
     * @return mixed
     */
    private function _processGiftMessage()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			/**
         * Saving of giftmessages
         */
        $this->_saveGiftMessage();
        /**
         * Importing gift message allow items from specific product grid
         */
        $data = $this->_importGiftMessageAllowQuoteItemsFromProducts();
        /**
         * Importing gift message allow items on update quote items
         */
        $this->_importGiftMessageAllowQuoteItemsFromItems();
        return $data;
		}
	}
    /**
     * Saves Gift message
     */
    private function _saveGiftMessage()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$giftmessages = $this->getRequest()->getPost('giftmessage');
        if ($giftmessages) {
            $this->_getGiftmessageSaveModel()->setGiftmessages($giftmessages)->saveAllInQuote();
        }
		}
	}
    /**
     * Retrieve gift message save model
     *
     * @return \Magento\GiftMessage\Model\Save
     */
    private function _getGiftmessageSaveModel()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->giftMessageSave;
		}
	}
    /**
     * Import git messages allowed quote items form products
     *
     * @return mixed
     */
    private function _importGiftMessageAllowQuoteItemsFromProducts()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($data = $this->getRequest()->getPost('add_products')) {
            $this->_getGiftmessageSaveModel()->importAllowQuoteItemsFromProducts(
                $this->jsonDataHelper->jsonDecode($data)
            );
            return $data;
        }
        return $data;
		}
	}
    /**
     * Import gift messages allow quote items from items
     */
    private function _importGiftMessageAllowQuoteItemsFromItems()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->getRequest()->getPost('update_items')) {
            $items = $this->getRequest()->getPost('item', []);
            $this->_getGiftmessageSaveModel()->importAllowQuoteItemsFromItems($items);
        }
		}
	}
    /**
     * Reload the quote and reset it on the current_quote registery
     *
     * @return $this
     */
    private function _reloadQuote()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_currentQuote = $this->quoteFactory->create()->load($this->getCurrentQuote()->getId());
        $this->_coreRegistry->unregister('current_quote');
        $this->_coreRegistry->register('current_quote', $this->_currentQuote);
        return $this;
		}
	}
}
