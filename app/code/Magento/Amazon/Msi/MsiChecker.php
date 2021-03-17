<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Msi;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Module\Manager as ModuleManager;

/**
 * Class MsiChecker
 */
class MsiChecker
{
    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param ModuleManager $moduleManager
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ModuleManager $moduleManager,
        ResourceConnection $resourceConnection
    ) {
        $this->moduleManager = $moduleManager;
        $this->connection = $resourceConnection->getConnection();
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @return bool
     */
    public function isMsiEnabled(): bool
    {
        return $this->moduleManager->isEnabled('Magento_Inventory');
    }

    /**
     * @return bool
     */
    public function isMsiReservationsEnabled(): bool
    {
        $reservationsEnabled = $this->moduleManager->isEnabled('Magento_InventoryReservations');
        $reservationTable = $this->resourceConnection->getTableName('inventory_reservation');
        $reservationTableExists = $this->connection->isTableExists($reservationTable);
        return $this->isMsiEnabled() && $reservationsEnabled && $reservationTableExists;
    }
}
