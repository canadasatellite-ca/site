<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Carrier;
use \Magento\Shipping\Model\Carrier\AbstractCarrier;
/**
 * Quotation shipping model
 */
trait QuotationShipping
{
    /**
     * Collect the shipping rates
     * - Only when using a quotation quote the shipping will be collected
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result|bool
     */
    private function collectRates(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quoteId = $this->getQuoteId($request);
        $this->quote = $this->getQuote($request);
        $sessionConfigData = $this->getSessionQuoteConfigData($quoteId);
        if (!($this->hasFixedShipping($sessionConfigData) ||
            $this->isBackend() ||
            $this->quote->getIsQuotationQuote() ||
            $this->existsInSession($quoteId))) {
            return false;
        }
        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();
        $shippingPrice = $this->getShippingPrice($sessionConfigData);
        if ($shippingPrice >= 0) {
            $shippingPrice = $this->quote->convertShippingPrice($shippingPrice, true);
            $method = $this->createResultMethod($shippingPrice);
            $result->append($method);
        }
        return $result;
		}
	}
    /**
     * Get the quote ID
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return int
     */
    private function getQuoteId(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quoteId = 0;
        $allItems = $request->getAllItems();
        if ($allItems && !empty($allItems)) {
            $quoteItem = reset($allItems);
            $quoteId = $quoteItem->getQuote()->getId();
        }
        return $quoteId;
		}
	}
    /**
     * Get the quote
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return int
     */
    private function getQuote(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quote = null;
        $allItems = $request->getAllItems();
        if ($allItems && !empty($allItems)) {
            $quoteItem = reset($allItems);
            $quote = $quoteItem->getQuote();
        }
        if ($quote === null || !($quote instanceof \Cart2Quote\Quotation\Model\Quote)) {
            $quoteId = $this->getQuoteId($request);
            $this->quote->load($quoteId);
            if ($quote !== null) {
                $this->quote->setData($quote->getData());
            }
            return $this->quote;
        }
        return $quote;
		}
	}
    /**
     * Get quote data from the session
     *
     * @param int $quoteId
     * @return array
     */
    private function getSessionQuoteConfigData($quoteId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$data = [];
        $configData = $this->quoteSession->getData(\Cart2Quote\Quotation\Model\Session::QUOTATION_STORE_CONFIG_DATA);
        if (isset($configData[$quoteId])) {
            $data = $configData[$quoteId];
        }
        return $data;
		}
	}
    /**
     * Has fixed shipping price
     *
     * @param array $configData
     * @return bool
     */
    private function hasFixedShipping($configData)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return isset($configData['fixed_shipping_price']);
		}
	}
    /**
     * Check if the request is done in the backend
     *
     * @return bool
     */
    private function isBackend()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->appState->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML;
		}
	}
    /**
     * Check if the quote id in the session is the same as the given quote id
     *
     * @param int $quoteId
     * @return bool
     */
    private function existsInSession($quoteId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->quoteSession->getQuoteId() == $quoteId;
		}
	}
    /**
     * Get the shipping price
     *
     * @param array $configData
     * @return float
     */
    private function getShippingPrice($configData)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$price = 0;
        if ($this->quote->getFixedShippingPrice()) {
            $price = $this->quote->getFixedShippingPrice();
        } elseif ($this->hasFixedShipping($configData)) {
            $price = $this->getFixedShippingPrice($configData);
        }
        return $price;
		}
	}
    /**
     * Get fixed shipping price
     *
     * @param array $configData
     * @return string
     */
    private function getFixedShippingPrice($configData)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $configData['fixed_shipping_price'];
		}
	}
    /**
     * Create the result method
     *
     * @param int|float $shippingPrice
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Method
     */
    private function createResultMethod($shippingPrice)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			/** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_rateMethodFactory->create();
        $method->setCarrier(self::CODE);
        $method->setCarrierTitle(__($this->getShippingTitle()));
        $method->setMethod(self::CODE);
        $method->setMethodTitle(__($this->getShippingMethod()));
        $method->setMethodDescription(__('Custom Price'));
        $method->setPrice($shippingPrice);
        $method->setCost($shippingPrice);
        return $method;
		}
	}
    /**
     * Get allowed methods
     *
     * @return array
     */
    private function getAllowedMethods()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return [self::CODE => __('Quote Shipping')];
		}
	}
    /**
     * Check if the Shipping method is Active
     *
     * @return bool
     */
    private function isActive()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->_scopeConfig->getValue(
            self::XML_PATH_CARRIER_QUOTATION_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		}
	}
    /**
     * Get quotation shipping title
     *
     * @return string
     */
    private function getShippingTitle()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$title = $this->_scopeConfig->getValue(self::XML_PATH_CARRIER_QUOTATION_TITLE);
        return isset($title) ? $title : 'Quote Shipping';
		}
	}
    /**
     * Get quotation shipping method name
     *
     * @return string
     */
    private function getShippingMethod()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$method = $this->_scopeConfig->getValue(self::XML_PATH_CARRIER_QUOTATION_METHOD);
        return isset($method) ? $method : 'Custom Price';
		}
	}
}
