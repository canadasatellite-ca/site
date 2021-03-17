<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Helper\Data;

/**
 * Interface LicenseMetadataInterface
 *
 * @package Cart2Quote\Quotation\Helper\Data
 */
interface LicenseInterface extends \Cart2Quote\Quotation\Model\LicenseInterface
{
    /**
     * Identifier for a invalid license
     */
    const NOT_VALID = 0;
    /**
     * Identifier for a valid trial
     */
    const VALID_TRIAL = 1;
    /**
     * Identifier for a invalid trial
     */
    const EXPIRED_TRIAL = 2;
    /**
     * Identifier for a pending license
     */
    const PENDING_LICENSE = 3;
    /**
     * Identifier for a valid license
     */
    const VALID_LICENSE = 4;
    /**
     * Identifier for a expired
     */
    const EXPIRED_LICENSE = 5;
    /**
     * Identifier for a cancelled subscription
     */
    const CANCELED_SUBSCRIPTION = 6;
    /**
     * Identifier for a valid subscription
     */
    const VALID_SUBSCRIPTION = 7;
    /**
     * Identifier if the dashboard is unreachable
     */
    const SERVER_UNREACHABLE = 8;
    /**
     * Identifier for a opensource license
     */
    const OPENSOURCE = 9;

    /**
     * @return int
     */
    public function getSimplifiedLicenseState();
}
