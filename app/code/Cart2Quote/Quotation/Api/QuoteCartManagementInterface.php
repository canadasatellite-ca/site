<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Api;

use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Interface QuoteCartManagementInterface
 * @package Cart2Quote\Quotation\Api
 */
interface QuoteCartManagementInterface
{
    /**
     * Returns information for the quote cart for a specified customer.
     *
     * @param int $customerId The customer ID.
     * @return \Cart2Quote\Quotation\Api\Data\QuoteCartInterface Cart object.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified customer does not exist.
     */
    public function getQuoteCartForCustomer($customerId);

    /**
     * @param int $customerId
     * @param int $storeId
     * @return int
     * @throws CouldNotSaveException
     */
    public function createEmptyQuoteCartForCustomer($customerId, $storeId);

    /**
     * Request a quote for a specified quotecart.
     *
     * @param int $customerId The customer ID.
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return array Quote
     */
    public function requestQuote($customerId);
}
