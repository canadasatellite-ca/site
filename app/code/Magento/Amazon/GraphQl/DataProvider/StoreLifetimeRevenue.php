<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\DataProvider;

use Magento\Amazon\Model\ResourceModel\Amazon\Order\OrderMetrics;

class StoreLifetimeRevenue
{
    /**
     * @var OrderMetrics
     */
    private $orderMetrics;

    private $reports;

    public function __construct(OrderMetrics $orderMetrics)
    {
        $this->orderMetrics = $orderMetrics;
    }

    /**
     * @param int $storeId
     * @param \Magento\Amazon\GraphQl\Context $context
     * @return float
     */
    public function getTotalRevenueByStoreId(int $storeId, \Magento\Amazon\GraphQl\Context $context): float
    {
        if (null === $this->reports) {
            $this->reports = $this->orderMetrics->getLifetimeSales($context->lifetimeReport()->getAll());
        }
        return (float)($this->reports[$storeId] ?? 0.0);
    }
}
