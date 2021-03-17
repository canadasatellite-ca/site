<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\ResourceModel\AbuseReport;

use Aheadworks\AdvancedReviews\Api\Data\AbuseReportInterface;
use Aheadworks\AdvancedReviews\Model\ResourceModel\AbstractCollection;
use Aheadworks\AdvancedReviews\Model\ResourceModel\AbuseReport as AbuseReportResource;
use Aheadworks\AdvancedReviews\Model\AbuseReport;

/**
 * Class Collection
 * @package Aheadworks\AdvancedReviews\Model\ResourceModel\AbuseReport
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = AbuseReportInterface::ID;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(AbuseReport::class, AbuseReportResource::class);
    }
}
