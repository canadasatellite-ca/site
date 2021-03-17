<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 * @codingStandardsIgnoreFile
 */

namespace Cart2Quote\Quotation\Api;

/**
 * Interface QuoteRepositoryInterface
 *
 * @package Cart2Quote\Quotation\Api
 */
interface QuoteRepositoryInterface
{

    /**
     * Enables an administrative user to return information for a specified cart.
     *
     * @param int $quoteId
     * @return \Cart2Quote\Quotation\Api\Data\QuoteCartInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($quoteId);

    /**
     * Save quote
     *
     * @param \Cart2Quote\Quotation\Api\Data\QuoteInterface $quote
     * @return void
     */
    public function saveQuote(\Cart2Quote\Quotation\Api\Data\QuoteInterface $quote);

    /**
     * Get all quotations with search
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Quote\Api\Data\CartSearchResultsInterface
     */
    public function getQuotesList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete quote
     *
     * @param int $quoteId
     * @param int[] $sharedStoreIds
     * @return void
     */
    public function deleteQuote($quoteId, array $sharedStoreIds);

    /**
     * Get items from a quote
     *
     * @param int $quoteId
     * @return \Magento\Quote\Api\Data\CartItemInterface[] Array of items.
     */
    public function getItems($quoteId);

    /**
     * Get active quote by customer Id
     *
     * @param int $customerId
     * @param int[] $sharedStoreIds
     * @return \Cart2Quote\Quotation\Api\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getActiveForCustomer($customerId, array $sharedStoreIds = []);

    /**
     * Save quote
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return void
     */
    public function save(\Magento\Quote\Api\Data\CartInterface $quote);

    /**
     * @param \Magento\Quote\Api\Data\CartItemInterface $cartItem
     * @return \Magento\Quote\Api\Data\CartItemInterface[] Array of items.
     */
    public function saveItems(\Magento\Quote\Api\Data\CartItemInterface $cartItem);

    /**
     * Removes the specified item from the specified quote.
     *
     * @param int $quoteId The quote ID.
     * @param int $itemId The item ID of the item to be removed.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified item or quote does not exist.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The item could not be removed.
     */
    public function deleteById($quoteId, $itemId);

    /**
     * @param int $quoteId
     * @return \Cart2Quote\Quotation\Api\Data\QuoteCartInterface|\Magento\Quote\Api\Data\CartInterface|\Magento\Quote\Model\Quote $quote
     */
    public function submitQuote($quoteId);
}
