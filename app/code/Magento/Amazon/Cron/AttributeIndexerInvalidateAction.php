<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Cron;

use Magento\Amazon\Api\ConfigManagementInterface;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\Indexer\AttributeProcessor;

/**
 * Class AttributeIndexerInvalidateAction
 */
class AttributeIndexerInvalidateAction
{
    /** @var ConfigManagementInterface $configManagement */
    private $configManagement;
    /** @var AttributeProcessor */
    private $attributeProcessor;
    /** @var AscClientLogger $ascClientLogger */
    protected $ascClientLogger;

    /**
     * @param ConfigManagementInterface $configManagement
     * @param AttributeProcessor $attributeProcessor
     * @param AscClientLogger $ascClientLogger
     */
    public function __construct(
        ConfigManagementInterface $configManagement,
        AttributeProcessor $attributeProcessor,
        AscClientLogger $ascClientLogger
    ) {
        $this->configManagement = $configManagement;
        $this->attributeProcessor = $attributeProcessor;
        $this->ascClientLogger = $ascClientLogger;
    }

    /**
     * Invalidates the attribute indexer associated
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
        try {
            $isAmazonCron = $this->configManagement->getCronSourceSetting();
            if ($isAmazonCron) {
                $indexer = $this->attributeProcessor->getIndexer();
                $indexer->invalidate();
                $this->attributeProcessor->updateMode();
                $this->ascClientLogger->info('Invalidating ASC attribute indexer completed.');
            }
        } catch (\Exception $e) {
            $this->ascClientLogger->critical($e);
        }
    }
}
