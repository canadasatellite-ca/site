<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model;

/**
 * Interface LicenseMetadataInterface
 *
 * @package Cart2Quote\Quotation\Model
 */
interface LicenseInterface
{
    /**
     * Get domain
     *
     * @return string
     */
    public function getDomain();

    /**
     * Get license type
     *
     * @return string
     */
    public function getLicenseType();

    /**
     * Get license state
     *
     * @return string
     */
    public function getLicenseState();

    /**
     * Get edition
     *
     * @return mixed
     */
    public function getEdition();

    /**
     * Check if is active state
     *
     * @return bool
     */
    public function isActiveState();

    /**
     * Check if is inactive state
     *
     * @return bool
     */
    public function isInactiveState();

    /**
     * Check if is pending state
     *
     * @return bool
     */
    public function isPendingState();

    /**
     * Check if is unreachable state
     *
     * @return bool
     */
    public function isUnreachable();

    /**
     * Check if is unreachable state (pass through)
     *
     * @return bool
     */
    public function isUnreachableState();

    /**
     * Get sent proposals amount
     *
     * @return int
     */
    public function getProposalAmount();

    /**
     * Get expiry date
     *
     * @return string|null
     */
    public function getExpiryDate();

    /**
     * Get order id
     *
     * @return string|null
     */
    public function getOrderId();

    /**
     * @param string $edition
     * @return bool
     */
    public function isAllowedForEdition($edition = 'opensource');
}
