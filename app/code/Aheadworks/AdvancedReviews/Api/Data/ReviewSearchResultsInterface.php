<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for review search results
 * @api
 */
interface ReviewSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get review list
     *
     * @return \Aheadworks\AdvancedReviews\Api\Data\ReviewInterface[]
     */
    public function getItems();

    /**
     * Set review list
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\ReviewInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
