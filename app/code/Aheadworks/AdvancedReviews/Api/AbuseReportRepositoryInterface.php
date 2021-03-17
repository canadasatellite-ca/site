<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Api;

/**
 * AbuseReport CRUD interface
 * @api
 */
interface AbuseReportRepositoryInterface
{
    /**
     * Save report
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\AbuseReportInterface $report
     * @return \Aheadworks\AdvancedReviews\Api\Data\AbuseReportInterface $report
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\AdvancedReviews\Api\Data\AbuseReportInterface $report);

    /**
     * Delete report
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\AbuseReportInterface $report
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Aheadworks\AdvancedReviews\Api\Data\AbuseReportInterface $report);

    /**
     * Retrieve report by id
     *
     * @param int $reportId
     * @return \Aheadworks\AdvancedReviews\Api\Data\AbuseReportInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($reportId);

    /**
     * Retrieve report by entity
     *
     * @param string $entityType
     * @param int $entityId
     * @return \Aheadworks\AdvancedReviews\Api\Data\AbuseReportInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByEntity($entityType, $entityId);

    /**
     * Ignore abuse reports for entities
     *
     * @param string $entityType
     * @param array $ids
     */
    public function ignoreForEntity($entityType, $ids);

    /**
     * Retrieve reports matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\AdvancedReviews\Api\Data\AbuseReportSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
