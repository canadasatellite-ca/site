<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\MiniCart;
/**
 * Trait Sidebar
 * @package Cart2Quote\Quotation\Model\MiniCart
 */
trait Sidebar
{
    /**
     * Remove item from miniquote
     *
     * @param int $itemId
     */
    private function removeQuoteItem($itemId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->cart->removeItem($itemId)->save();
        $this->cart->save();
		}
	}
    /**
     * Update miniquote item quantity
     *
     * @param int $itemId
     * @param int $itemQty
     * @return \Magento\Quote\Model\Quote\Item|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function updateQuoteItem($itemId, $itemQty)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$params = [];
        if (isset($itemQty)) {
            $filter = new \Zend_Filter_LocalizedToNormalized(
                [
                    'locale' => $this->resolver->getLocale()
                ]
            );
            $params['qty'] = $filter->filter($itemQty);
        }
        $quoteItem = $this->cart->getQuote()->getItemById($itemId);
        if (!$quoteItem) {
            throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t find the quote item.'));
        }
        $item = $this->cart->updateItem($itemId, new \Magento\Framework\DataObject($params));
        if (is_string($item)) {
            throw new \Magento\Framework\Exception\LocalizedException(__($item));
        }
        if ($item->getHasError()) {
            throw new \Magento\Framework\Exception\LocalizedException(__($item->getMessage()));
        }
        $this->cart->save();
        return $item;
		}
	}
    /**
     * Compile response data
     *
     * @param string $error
     * @return array
     */
    private function getResponseData($error = '')
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (empty($error)) {
            $response = [
                'success' => true,
            ];
        } else {
            $response = [
                'success' => false,
                'error_message' => $error,
            ];
        }
        return $response;
		}
	}
}
