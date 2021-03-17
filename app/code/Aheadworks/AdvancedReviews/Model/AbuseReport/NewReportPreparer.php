<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\AbuseReport;

use Aheadworks\AdvancedReviews\Api\Data\AbuseReportInterfaceFactory;
use Aheadworks\AdvancedReviews\Api\Data\AbuseReportInterface;
use Aheadworks\AdvancedReviews\Model\Source\AbuseReport\Status;

/**
 * Class NewReportPreparer
 * @package Aheadworks\AdvancedReviews\Model\AbuseReport
 */
class NewReportPreparer
{
    /**
     * @var AbuseReportInterfaceFactory
     */
    private $reportDataFactory;

    /**
     * @param AbuseReportInterfaceFactory $reportDataFactory
     */
    public function __construct(
        AbuseReportInterfaceFactory $reportDataFactory
    ) {
        $this->reportDataFactory = $reportDataFactory;
    }

    /**
     * Prepare new report instance
     *
     * @param string $entityType
     * @param int $entityId
     * @return AbuseReportInterface
     */
    public function prepare($entityType, $entityId)
    {
        /** @var AbuseReportInterface $abuseReport */
        $abuseReport = $this->reportDataFactory->create();

        $abuseReport
            ->setEntityType($entityType)
            ->setEntityId($entityId)
            ->setStatus(Status::getDefaultStatus());

        return $abuseReport;
    }
}
