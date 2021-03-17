<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\License\Setup;

use Cart2Quote\License\Cache\Type\License;
use Magento\Framework\App\Cache\Manager;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class UpgradeData
 *
 * @package Cart2Quote\License\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var Manager
     */
    private $cacheManager;

    /**
     * UpgradeData constructor
     *
     * @param Manager $cacheManager
     */
    public function __construct(Manager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $enabledTypes = $this->cacheManager->setEnabled([License::TYPE_IDENTIFIER], true);
        $this->cacheManager->clean($enabledTypes);
    }
}
