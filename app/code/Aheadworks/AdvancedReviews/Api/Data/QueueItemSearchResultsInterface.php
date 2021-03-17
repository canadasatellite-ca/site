<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface QueueItemSearchResultsInterfaceInterface
 * @package Aheadworks\AdvancedReviews\Api\Data
 */
interface QueueItemSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get queue items list
     *
     * @return QueueItemInterface[]
     */
    public function getItems();

    /**
     * Set queue items list
     *
     * @param QueueItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
