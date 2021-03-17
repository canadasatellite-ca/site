<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Api;

/**
 * Interface QuoteCartItemRepositoryInterface
 * @package Cart2Quote\Quotation\Api
 */
interface QuoteCartItemRepositoryInterface
{
    /**
     * @param int $customerId The customer ID.
     * @return \Magento\Quote\Api\Data\CartItemInterface[] Array of items.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function getList($customerId);

    /**
     * @param int $customerId The customer ID.
     * @param int $itemId
     * @return bool
     */
    public function deleteById($customerId, $itemId);

    /**
     * @param int $customerId The customer ID.
     * @param \Magento\Quote\Api\Data\CartItemInterface $cartItem
     * @return \Magento\Quote\Api\Data\CartItemInterface Item.
     */
    public function save($customerId, \Magento\Quote\Api\Data\CartItemInterface $cartItem);

    /**
     * @param int $customerId The customer ID.
     * @param \Magento\Quote\Api\Data\CartItemInterface $cartItem
     * @return \Magento\Quote\Api\Data\CartItemInterface Item.
     */
    public function editQuoteItem($customerId, \Magento\Quote\Api\Data\CartItemInterface $cartItem);
}
