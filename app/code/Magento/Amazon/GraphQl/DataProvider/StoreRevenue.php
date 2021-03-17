<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\DataProvider;

use Magento\Amazon\Model\ResourceModel\Amazon\Order\OrderMetrics;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class StoreRevenue
{
    /**
     * @var OrderMetrics
     */
    private $orderMetrics;

    private $results;
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    public function __construct(OrderMetrics $orderMetrics, TimezoneInterface $timezone)
    {
        $this->orderMetrics = $orderMetrics;
        $this->timezone = $timezone;
    }

    public function getRevenueByStoreId(
        int $merchantId,
        int $days,
        \Magento\Amazon\GraphQl\Context $context,
        bool $filterEmpty = false
    ): array {
        if (null === $this->results) {
            $merchantIds = $context->revenueReportIds()->getAll();
            $periodInDays = (int)(max($context->revenueReportDays()->getAll()) ?: 0);
            $this->results = $this->getMetrics($merchantIds, $periodInDays);
        }
        $merchantMetrics = $this->results[$merchantId] ?? [];
        $result = array_slice($merchantMetrics, 0, $days);
        if ($filterEmpty) {
            $result = $this->filterEmpty($result);
        }
        return $result;
    }

    private function filterEmpty(array $data): array
    {
        return array_filter($data, static function ($value) {
            return ($value['revenue'] ?? 0) !== 0.0;
        });
    }

    private function getMetrics(array $merchantIds, int $days): array
    {
        $timezone = new \DateTimeZone($this->timezone->getConfigTimezone());
        $dates = array_map(static function ($daysAgo) use ($timezone) {
            $xDaysAgo = new \DateTimeImmutable("$daysAgo days ago", $timezone);
            return $xDaysAgo->format('Y-m-d');
        }, range(0, $days - 1));

        $prefilledData = array_combine($dates, array_map(static function ($date) {
            return ['date' => $date, 'revenue' => 0.0];
        }, $dates));

        $result = array_fill_keys($merchantIds, $prefilledData);

        $orderMetrics = $this->orderMetrics->getRevenue($merchantIds, $days);

        foreach ($orderMetrics as $metric) {
            $merchantId = $metric['merchant_id'];
            $date = $metric['date'];
            $revenue = $metric['revenue'];
            $result[$merchantId][$date]['revenue'] = (float)$revenue;
        }
        return $result;
    }
}
