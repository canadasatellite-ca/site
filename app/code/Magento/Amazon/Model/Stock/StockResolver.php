<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model\Stock;

use Magento\Amazon\Msi\MsiChecker;

/**
 * Class StockResolver
 */
class StockResolver
{
    /**
     * @var MsiChecker
     */
    private $msiChecker;

    /**
     * @var array
     */
    private $stockPool;

    /**
     * StockResolver constructor.
     * @param MsiChecker $msiChecker
     * @param array $stocks
     */
    public function __construct(
        MsiChecker $msiChecker,
        array $stocks = []
    ) {
        $this->msiChecker = $msiChecker;
        $this->stockPool = $stocks;
    }

    /**
     * @return StockInterface
     */
    public function resolve(): StockInterface
    {
        $stockHandler = $this->stockPool['legacy_stock'];
        $msiEnabled = $this->msiChecker->isMsiEnabled();
        $msiReservationsEnabled = $this->msiChecker->isMsiReservationsEnabled();

        if ($msiEnabled) {
            if ($msiReservationsEnabled) {
                $stockHandler = $this->stockPool['msi_stock_with_reservations'];
            } else {
                $stockHandler = $this->stockPool['msi_stock'];
            }
        }

        return $stockHandler;
    }
}
