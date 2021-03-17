<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model;

use Aheadworks\AdvancedReviews\Api\StatisticsRepositoryInterface;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Statistics as StatisticsResourceModel;
use Aheadworks\AdvancedReviews\Api\Data\StatisticsInterface;
use Aheadworks\AdvancedReviews\Api\Data\StatisticsInterfaceFactory;

/**
 * Class StatisticsRepository
 *
 * @package Aheadworks\AdvancedReviews\Model
 */
class StatisticsRepository implements StatisticsRepositoryInterface
{
    /**
     * @var StatisticsResourceModel
     */
    private $resource;

    /**
     * @var StatisticsInterfaceFactory
     */
    private $statisticsInterfaceFactory;

    /**
     * @param StatisticsResourceModel $resource
     * @param StatisticsInterfaceFactory $statisticsInterfaceFactory
     */
    public function __construct(
        StatisticsResourceModel $resource,
        StatisticsInterfaceFactory $statisticsInterfaceFactory
    ) {
        $this->resource = $resource;
        $this->statisticsInterfaceFactory = $statisticsInterfaceFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getByProductId($productId, $storeId = null)
    {
        /** @var StatisticsInterface $statisticsInstance */
        $statisticsInstance = $this->statisticsInterfaceFactory->create();
        $this->resource->load($statisticsInstance, $productId, $storeId);
        return $statisticsInstance;
    }
}
