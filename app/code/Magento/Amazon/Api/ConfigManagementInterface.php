<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api;

/**
 * Interface ConfigManagementInterface
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface ConfigManagementInterface
{
    /**
     * Returns the value of show chart setting
     * Options include command line (CLI) or native Magento CRON
     *
     * return int
     */
    public function getShowChartSetting();

    /**
     * Returns the value of cron source setting
     * Options include command line (CLI) or native Magento CRON
     *
     * return int
     */
    public function getCronSourceSetting();

    /**
     * Returns the value of days to hold log records per global settings
     *
     * return int
     */
    public function getLogHistorySetting();
}
