<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Frontend;

use Magento\Framework\ObjectManagerInterface;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Collection;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Model\Source\Review\Status as ReviewStatusSource;
use Aheadworks\AdvancedReviews\Model\DateTime\Formatter as DateTimeFormatter;

/**
 * Class CollectionFactory
 *
 * @package Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Frontend
 */
class CollectionFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManagerInterface
     */
    private $objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    private $instanceName = null;

    /**
     * @var DateTimeFormatter
     */
    private $dateTimeFormatter;

    /**
     * Factory constructor
     *
     * @param ObjectManagerInterface $objectManager
     * @param DateTimeFormatter $dateTimeFormatter
     * @param string $instanceName
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        DateTimeFormatter $dateTimeFormatter,
        $instanceName = Collection::class
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
        $this->dateTimeFormatter = $dateTimeFormatter;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @param bool $addDisplayStatusesFilter = true
     * @return Collection
     */
    public function create(array $data = [], $addDisplayStatusesFilter = true)
    {
        $collection = $this->objectManager->create($this->instanceName, $data);
        return $this->applyDefaultFrontendFilters($collection, $addDisplayStatusesFilter);
    }

    /**
     * Apply default frontend filters to the created collection
     *
     * @param Collection $collection
     * @param bool $addDisplayStatusesFilter
     * @return mixed
     */
    protected function applyDefaultFrontendFilters($collection, $addDisplayStatusesFilter)
    {
        $collection->addFieldToFilter(
            ReviewInterface::CREATED_AT,
            [
                'lteq' => $this->getCurrentFormattedDate()
            ]
        );
        if ($addDisplayStatusesFilter) {
            $collection->addFieldToFilter(ReviewInterface::STATUS, ReviewStatusSource::getDisplayStatuses());
        }
        return $collection;
    }

    /**
     * Retrieve current formatted date
     *
     * @return string
     */
    protected function getCurrentFormattedDate()
    {
        return $this->dateTimeFormatter->getDateTimeInDbFormat(null);
    }
}
