<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Cron;

use Magento\Amazon\Api\ConfigManagementInterface;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\Indexer\StockProcessor;

/**
 * Class StockIndexerInvalidateAction
 */
class StockIndexerInvalidateAction
{
    /** @var ConfigManagementInterface $configManagement */
    private $configManagement;
    /** @var StockProcessor */
    private $stockProcessor;
    /** @var AscClientLogger $ascLogger */
    protected $ascLogger;

    /**
     * @param ConfigManagementInterface $configManagement
     * @param StockProcessor $stockProcessor
     * @param AscClientLogger $ascLogger
     */
    public function __construct(
        ConfigManagementInterface $configManagement,
        StockProcessor $stockProcessor,
        AscClientLogger $ascLogger
    ) {
        $this->configManagement = $configManagement;
        $this->stockProcessor = $stockProcessor;
        $this->ascLogger = $ascLogger;
    }

    /**
     * Invalidates the stock indexer associated
     * with the Amazon Sales Channel
     *
     * If Magento CRON is disabled in settings, it suppresses
     * execution.
     *
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
        $this->ascLogger->debug('Invalidates the ASC stock indexer.');
        try {
            $isAmazonCron = $this->configManagement->getCronSourceSetting();
            if ($isAmazonCron) {
                $indexer = $this->stockProcessor->getIndexer();
                $indexer->invalidate();
                $this->stockProcessor->updateMode();
                $this->ascLogger->notice('Cron triggered task to invalidate ASC stock indexer completed.');
            }
        } catch (\Exception $e) {
            $this->ascLogger->critical($e);
        }
    }
}
