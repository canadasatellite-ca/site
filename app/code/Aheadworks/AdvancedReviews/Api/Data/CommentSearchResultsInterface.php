<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface CommentSearchResultsInterface
 * @package Aheadworks\AdvancedReviews\Api\Data
 */
interface CommentSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get comment list
     *
     * @return \Aheadworks\AdvancedReviews\Api\Data\CommentInterface[]
     */
    public function getItems();

    /**
     * Set comment list
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\CommentInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
