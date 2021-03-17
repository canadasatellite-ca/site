<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 * @codingStandardsIgnoreFile
 */

namespace Cart2Quote\Quotation\Api\Data;

use Magento\Quote\Api\Data\CartSearchResultsInterface;

/**
 * Quote search result interface.
 *
 * @api
 */
interface QuoteSearchResultsInterface extends CartSearchResultsInterface
{
    /**
     * Get carts list.
     *
     * @return \Cart2Quote\Quotation\Api\Data\QuoteInterface[]
     */
    public function getItems();

    /**
     * Set carts list.
     *
     * @param \Cart2Quote\Quotation\Api\Data\QuoteInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
