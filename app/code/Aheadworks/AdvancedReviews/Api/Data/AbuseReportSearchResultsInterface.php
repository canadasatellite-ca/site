<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface AbuseReportSearchResultsInterface
 * @package Aheadworks\AdvancedReviews\Api\Data
 */
interface AbuseReportSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get reports list
     *
     * @return \Aheadworks\AdvancedReviews\Api\Data\AbuseReportInterface[]
     */
    public function getItems();

    /**
     * Set reports list
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\AbuseReportInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
