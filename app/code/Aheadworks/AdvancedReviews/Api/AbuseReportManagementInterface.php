<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Api;

/**
 * Interface AbuseReportManagementInterface
 * @package Aheadworks\AdvancedReviews\Api
 */
interface AbuseReportManagementInterface
{
    /**
     * Abuse report for review
     *
     * @param int $reviewId
     * @param int $storeId
     * @return \Aheadworks\AdvancedReviews\Api\Data\AbuseReportInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function reportReview($reviewId, $storeId);

    /**
     * Abuse report for comment
     *
     * @param int $commentId
     * @param int $storeId
     * @return \Aheadworks\AdvancedReviews\Api\Data\AbuseReportInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function reportComment($commentId, $storeId);

    /**
     * Ignore abuse reports for reviews
     *
     * @param array $reviewIds
     * @param bool $alsoForComments
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function ignoreAbuseForReviews($reviewIds, $alsoForComments = false);

    /**
     * Ignore abuse reports for comments
     *
     * @param array $commentIds
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function ignoreAbuseForComments($commentIds);
}
