<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Cron;

use Magento\Amazon\Api\ConfigManagementInterface;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\Indexer\PricingProcessor;

/**
 * Class PricingIndexerInvalidateAction
 */
class PricingIndexerInvalidateAction
{
    /** @var ConfigManagementInterface $configManagement */
    private $configManagement;
    /** @var PricingProcessor */
    private $pricingProcessor;
    /** @var AscClientLogger $ascLogger */
    protected $ascLogger;

    /**
     * @param ConfigManagementInterface $configManagement
     * @param PricingProcessor $pricingProcessor
     * @param AscClientLogger $ascLogger
     */
    public function __construct(
        ConfigManagementInterface $configManagement,
        PricingProcessor $pricingProcessor,
        AscClientLogger $ascLogger
    ) {
        $this->configManagement = $configManagement;
        $this->pricingProcessor = $pricingProcessor;
        $this->ascLogger = $ascLogger;
    }

    /**
     * Invalidates the pricing indexer associated
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
        $this->ascLogger->debug('Invalidates the ASC pricing indexer.');
        try {
            $isAmazonCron = $this->configManagement->getCronSourceSetting();
            if ($isAmazonCron) {
                $indexer = $this->pricingProcessor->getIndexer();
                $indexer->invalidate();
                $this->pricingProcessor->updateMode();
                $this->ascLogger->notice('Cron triggered task to invalidate ASC pricing indexer completed.');
            }
        } catch (\Exception $e) {
            $this->ascLogger->critical($e);
        }
    }
}
