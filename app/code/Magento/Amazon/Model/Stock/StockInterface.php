<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Stock;

/**
 * Interface StockInterface
 */
interface StockInterface
{
    /**
     * Syncs Amazon listing quantity to stock quantity
     * @param int $merchantId
     */

    public function setAmazonListingQtyToStockQty(int $merchantId);
}
