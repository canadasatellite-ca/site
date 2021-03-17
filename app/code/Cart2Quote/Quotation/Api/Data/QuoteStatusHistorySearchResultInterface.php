<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 * @codingStandardsIgnoreFile
 */

namespace Cart2Quote\Quotation\Api\Data;

/**
 * Quote status history search result interface.
 * An quote is a document that a web store issues to a customer.
 * Magento generates a quotation quote that lists the product items, billing and shipping addresses,
 * and shipping and payment methods. A corresponding external document, known as
 * a quote proposal, is emailed to the customer.
 * @api
 */
interface QuoteStatusHistorySearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Gets collection items.
     *
     * @return \Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Set collection items.
     *
     * @param \Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
