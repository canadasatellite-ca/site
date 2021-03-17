<?php

namespace Magento\Amazon\Plugin;

use Magento\Framework\Module\Manager as ModuleManager;

/**
 * Class RemoveRedundantInventoryMview
 */
class RemoveRedundantInventoryMview
{
    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * RemoveRedundantInventoryMview constructor.
     * @param ModuleManager $moduleManager
     */
    public function __construct(ModuleManager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param \Magento\Framework\Mview\Config\Reader $configReader
     * @param array $result
     * @return array
     */
    public function afterRead(\Magento\Framework\Mview\Config\Reader $configReader, array $result): array
    {
        if ($this->moduleManager->isEnabled('Magento_Inventory')) {
            unset($result['channel_amazon_stock']['subscriptions']['cataloginventory_stock_item']);
        } else {
            unset($result['channel_amazon_stock']['subscriptions']['inventory_source_item']);
        }
        return $result;
    }
}
