<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Agreements;

use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Source\Review\Agreements\DisplayMode as AgreementsDisplayMode;

/**
 * Class Checker
 *
 * @package Aheadworks\AdvancedReviews\Model\Agreements
 */
class Checker
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Check if agreements are enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function areAgreementsEnabled($storeId)
    {
        return $this->config->areAgreementsEnabled($storeId);
    }

    /**
     * Check if need to show agreements for guests
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isNeedToShowForGuests($storeId)
    {
        $displayMode = $this->config->getAgreementsDisplayMode($storeId);
        return $displayMode == AgreementsDisplayMode::GUESTS_ONLY
            || $displayMode == AgreementsDisplayMode::EVERYONE;
    }

    /**
     * Check if need to show agreements for customers
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isNeedToShowForCustomers($storeId)
    {
        $displayMode = $this->config->getAgreementsDisplayMode($storeId);
        return $displayMode == AgreementsDisplayMode::EVERYONE;
    }
}
