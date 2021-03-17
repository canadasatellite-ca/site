<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Api\Data\Email;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface SubscriberSearchResultsInterface
 *
 * @package Aheadworks\AdvancedReviews\Api\Data\Email
 */
interface SubscriberSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get email subscriber list
     *
     * @return \Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface[]
     */
    public function getItems();

    /**
     * Set email subscriber list
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
