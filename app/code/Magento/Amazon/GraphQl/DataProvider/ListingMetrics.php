<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\DataProvider;

use Magento\Amazon\GraphQl\Context;
use Magento\Amazon\Model\Amazon\Definitions;

class ListingMetrics
{
    /**
     * @var \Magento\Amazon\Model\ResourceModel\Amazon\ListingMetrics
     */
    private $listingMetrics;

    private static $statuses = [
        'active' => [
            Definitions::REMOVE_IN_PROGRESS_LIST_STATUS,
            Definitions::TOBEENDED_LIST_STATUS,
            Definitions::CONDITION_OVERRIDE_LIST_STATUS,
            Definitions::ACTIVE_LIST_STATUS
        ],
        'inactive' => [
            Definitions::ERROR_LIST_STATUS
        ],
        'incomplete' => [
            Definitions::MISSING_CONDITION_LIST_STATUS,
            Definitions::NOMATCH_LIST_STATUS,
            Definitions::MULTIPLE_LIST_STATUS,
            Definitions::VARIANTS_LIST_STATUS
        ],
        'inProgress' => [
            Definitions::VALIDATE_ASIN_LIST_STATUS,
            Definitions::READY_LIST_STATUS,
            Definitions::LIST_IN_PROGRESS_LIST_STATUS,
            Definitions::GENERAL_SEARCH_LIST_STATUS
        ],
        'thirdParty' => [
            Definitions::VALIDATE_ASIN_LIST_STATUS,
            Definitions::READY_LIST_STATUS,
            Definitions::LIST_IN_PROGRESS_LIST_STATUS,
            Definitions::GENERAL_SEARCH_LIST_STATUS
        ],
    ];

    private $metrics;

    public function __construct(
        \Magento\Amazon\Model\ResourceModel\Amazon\ListingMetrics $listingMetrics
    ) {
        $this->listingMetrics = $listingMetrics;
    }

    public function getListingMetricForStore(int $storeId, string $metricType, Context $context): int
    {
        if (!isset($this->metrics[$metricType]) && isset(self::$statuses[$metricType])) {
            $this->metrics[$metricType] = $this->listingMetrics->countListingsByStatusesPerMerchant(
                self::$statuses[$metricType],
                $context->listingMetricIds($metricType)->getAll()
            );
        }
        return (int)($this->metrics[$metricType][$storeId] ?? 0);
    }
}
