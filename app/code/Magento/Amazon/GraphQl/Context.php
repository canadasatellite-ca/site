<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl;

use Magento\Amazon\GraphQl\Container\IdsAndFields;
use Magento\Amazon\GraphQl\Container\IdsContainer;
use Magento\Amazon\GraphQl\Container\StoreOrdersContainer;
use Magento\Amazon\GraphQl\Container\UniqueContainer;

class Context
{
    /**
     * @var array
     */
    private $containers = [];
    /**
     * @var IdsContainer[]
     */
    private $listingMetrics = [];

    /**
     * @return IdsAndFields
     */
    public function stores(): IdsAndFields
    {
        if (!isset($this->containers['stores'])) {
            $this->containers['stores'] = new IdsAndFields();
        }
        return $this->containers['stores'];
    }

    /**
     * @return Container\StoreOrdersContainer
     */
    public function storeOrders(): StoreOrdersContainer
    {
        if (!isset($this->containers['storeOrders'])) {
            $this->containers['storeOrders'] = new StoreOrdersContainer();
        }
        return $this->containers['storeOrders'];
    }

    /**
     * @param string $metricName
     * @return IdsContainer
     */
    public function listingMetricIds(string $metricName): IdsContainer
    {
        if (!isset($this->listingMetrics[$metricName])) {
            $this->listingMetrics[$metricName] = new IdsContainer();
        }
        return $this->listingMetrics[$metricName];
    }

    /**
     * @return IdsContainer
     */
    public function lifetimeReport(): IdsContainer
    {
        if (!isset($this->containers['lifetimeReport'])) {
            $this->containers['lifetimeReport'] = new IdsContainer();
        }
        return $this->containers['lifetimeReport'];
    }

    /**
     * @return IdsContainer
     */
    public function revenueReportIds(): IdsContainer
    {
        if (!isset($this->containers['revenueReportIds'])) {
            $this->containers['revenueReportIds'] = new IdsContainer();
        }
        return $this->containers['revenueReportIds'];
    }

    /**
     * @return UniqueContainer
     */
    public function revenueReportDays(): UniqueContainer
    {
        if (!isset($this->containers['revenueReportDays'])) {
            $this->containers['revenueReportDays'] = new UniqueContainer();
        }
        return $this->containers['revenueReportDays'];
    }

    /**
     * @return IdsAndFields
     */
    public function storeOrderItems(): IdsAndFields
    {
        if (!isset($this->containers['storeOrderItems'])) {
            $this->containers['storeOrderItems'] = new IdsAndFields();
        }
        return $this->containers['storeOrderItems'];
    }

    /**
     * @return IdsAndFields
     */
    public function storeOrderTracking(): IdsAndFields
    {
        if (!isset($this->containers['storeOrderTracking'])) {
            $this->containers['storeOrderTracking'] = new IdsAndFields();
        }
        return $this->containers['storeOrderTracking'];
    }
}
