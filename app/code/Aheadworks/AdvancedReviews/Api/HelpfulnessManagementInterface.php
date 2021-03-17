<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Api;

/**
 * Interface HelpfulnessManagementInterface
 * @package Aheadworks\AdvancedReviews\Api
 */
interface HelpfulnessManagementInterface
{
    /**
     * Vote for review
     *
     * @param int $reviewId
     * @param string $action
     * @return \Aheadworks\AdvancedReviews\Api\Data\VoteResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function vote($reviewId, $action = '');
}
