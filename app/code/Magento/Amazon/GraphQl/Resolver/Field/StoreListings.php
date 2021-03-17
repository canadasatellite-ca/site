<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Resolver\Field;

use GraphQL\Deferred;
use GraphQL\Type\Definition\ResolveInfo;
use Magento\Amazon\GraphQl\Context;
use Magento\Amazon\GraphQl\DataProvider\ListingMetrics;

class StoreListings implements \Magento\Amazon\GraphQl\Resolver\ResolverInterface
{
    /**
     * @var ListingMetrics
     */
    private $listingMetrics;

    public function __construct(
        ListingMetrics $listingMetrics
    ) {
        $this->listingMetrics = $listingMetrics;
    }

    public function resolve(
        $parent,
        array $args,
        Context $context,
        ResolveInfo $info
    ) {
        $merchantId = (int)$parent['id'];

        $metricTypes = array_keys($info->getFieldSelection());
        foreach ($metricTypes as $metricType) {
            $metricContainer = $context->listingMetricIds($metricType);
            $metricContainer->add($merchantId);
        }

        return new Deferred(function () use ($merchantId, $metricTypes, $context): array {
            $data = [];
            foreach ($metricTypes as $metricType) {
                $data[$metricType] = $this->listingMetrics->getListingMetricForStore($merchantId, $metricType, $context);
            }
            return $data;
        });
    }
}
