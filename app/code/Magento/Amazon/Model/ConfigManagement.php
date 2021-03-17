<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model;

use Magento\Amazon\Api\ConfigManagementInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class ConfigManagement
 */
class ConfigManagement implements ConfigManagementInterface
{
    const SHOW_CHART_DEFAULT = 1;
    const CRON_SOURCE_DEFAULT = 1;
    const LOG_HISTORY_DEFAULT = 7;

    /** @var ScopeConfigInterface $scopeConfig */
    protected $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Returns the value of show chart setting
     * Options include command line (CLI) or native Magento CRON
     *
     * @return int
     */
    public function getShowChartSetting()
    {
        /** @var bool */
        return $this->scopeConfig->getValue('saleschannels/general/show_chart', ScopeInterface::SCOPE_STORE);
    }

    /**
     * {@inheritdoc}
     */
    public function getCronSourceSetting()
    {
        /** @var bool */
        $value = $this->scopeConfig->getValue('saleschannels/general/cron_source', ScopeInterface::SCOPE_STORE);

        return ($value) ? $value : self::CRON_SOURCE_DEFAULT;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogHistorySetting()
    {
        /** @var bool */
        $value = $this->scopeConfig->getValue('saleschannels/general/log_history', ScopeInterface::SCOPE_STORE);

        return ($value) ? $value : self::LOG_HISTORY_DEFAULT;
    }
}
