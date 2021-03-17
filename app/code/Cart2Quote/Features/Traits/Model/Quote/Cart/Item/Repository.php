<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Cart\Item;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
/**
 * Trait Repository
 * @package Cart2Quote\Quotation\Model\Quote\Cart\Item
 */
trait Repository
{
    /**
     * Get array of items from active quote cart for logged in customer
     *
     * @param int $customerId The customer ID.
     * @return \Magento\Quote\Api\Data\CartItemInterface[] Array of items.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    private function getList($customerId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$output = [];
        /** @var  \Cart2Quote\Quotation\Api\QuoteRepositoryInterface $quote */
        $quote = $this->quotationRepository->getActiveForCustomer($customerId);
        /** @var  \Magento\Quote\Model\Quote\Item  $item */
        foreach ($quote->getAllVisibleItems() as $item) {
            $item = $this->cartItemOptionsProcessor->addProductOptions($item->getProductType(), $item);
            $output[] = $this->cartItemOptionsProcessor->applyCustomOptions($item);
        }
        return $output;
		}
	}
    /**
     * @param int $customerId The customer ID.
     * @param \Magento\Quote\Api\Data\CartItemInterface $cartItem
     * @return \Magento\Quote\Api\Data\CartItemInterface Item.
     * @throws NoSuchEntityException
     */
    private function save($customerId, \Magento\Quote\Api\Data\CartItemInterface $cartItem)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			/** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quotationRepository->getActiveForCustomer($customerId);
        $quoteItems = $quote->getAllVisibleItems();
        $quoteItems[] = $cartItem;
        $quote->setItems($quoteItems);
        $this->quoteRepository->save($quote);
        $lastAddedItem = $quote->getLastAddedItem();
        return $lastAddedItem;
		}
	}
    /**
     * @param int $customerId The customer ID.
     * @param \Magento\Quote\Api\Data\CartItemInterface $cartItem
     * @return \Magento\Quote\Api\Data\CartItemInterface Item.
     * @throws NoSuchEntityException
     */
    private function editQuoteItem($customerId, \Magento\Quote\Api\Data\CartItemInterface $cartItem)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quote = $this->quotationRepository->getActiveForCustomer($customerId);
        $quoteItems = $quote->getAllVisibleItems();
        foreach ($quoteItems as $key => $quoteItem) {
            if($quoteItem->getSku() == $cartItem->getSku()) {
                if (!empty($quoteItem->getItemId())) {
                    $quote->removeItem($quoteItem->getItemId());
                }
                $quoteItems[$key] = $cartItem;
            }
        }
        $quote->setItems($quoteItems);
        $this->quoteRepository->save($quote);
        $lastAddedItem = $quote->getLastAddedItem();
        return $lastAddedItem;
		}
	}
    /**
     * Delete specified item in quote cart for logged in customer
     *
     * @param int $customerId
     * @param int $itemId
     * @return bool
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    private function deleteById($customerId, $itemId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			/** @var \Cart2Quote\Quotation\Api\QuoteRepositoryInterface $quote */
        $quote = $this->quotationRepository->getActiveForCustomer($customerId);
        $quoteItem = $quote->getItemById($itemId);
        if (!$quoteItem) {
            throw new NoSuchEntityException(
                __('The %1 Cart doesn\'t contain the %2 item.', $quote->getId(), $itemId)
            );
        }
        try {
            $quote->removeItem($itemId);
            $quote->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__("The item couldn't be removed from the quote."));
        }
        return true;
		}
	}
}
