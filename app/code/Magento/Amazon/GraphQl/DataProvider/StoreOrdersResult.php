<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\DataProvider;

class StoreOrdersResult
{
    /**
     * @var array
     */
    private $ordersData;
    /**
     * @var int|null
     */
    private $totalCount;

    /**
     * @var string|null
     */
    private $lastCursor;
    /**
     * @var bool
     */
    private $hasNextPage;

    public function __construct(array $ordersData, ?int $totalCount, bool $hasNextPage)
    {
        $this->ordersData = $ordersData;
        $this->totalCount = $totalCount;
        $this->hasNextPage = $hasNextPage;
    }

    /**
     * @return array
     */
    public function getOrdersData(): array
    {
        return $this->ordersData;
    }

    /**
     * @return int|null
     */
    public function getTotalCount(): ?int
    {
        return $this->totalCount;
    }

    public function getEndCursor(): ?string
    {
        if (!empty($this->ordersData)) {
            $lastOrder = end($this->ordersData);
            if ($lastOrder && isset($lastOrder['cursor'])) {
                $this->lastCursor = $lastOrder['cursor'];
            }
        }
        return $this->lastCursor;
    }

    /**
     * @return bool
     */
    public function hasNextPage(): bool
    {
        return $this->hasNextPage;
    }
}
