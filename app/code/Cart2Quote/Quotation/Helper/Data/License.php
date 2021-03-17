<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Helper\Data;

/**
 * Class License
 *
 * @package Cart2Quote\Quotation\Helper\Data
 */
class License implements \Cart2Quote\Quotation\Helper\Data\LicenseInterface
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    private $backendUrl;

    /**
     * License constructor.
     *
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     */
    public function __construct(\Magento\Backend\Model\UrlInterface $backendUrl)
    {
        $this->backendUrl = $backendUrl;
    }

    /**
     * @return string|null
     */
    public function getExpiryDate()
    {
        return null;
    }

    /**
     * Get domain
     *
     * @return string
     */
    public function getDomain()
    {
        // @codingStandardsIgnoreLine
        return parse_url($this->backendUrl->getBaseUrl(), PHP_URL_HOST);
    }

    /**
     * Get licensen type
     *
     * @return string
     */
    public function getLicenseType()
    {
        return 'one_off';
    }

    /**
     * Get license state
     *
     * @return string
     */
    public function getLicenseState()
    {
        return 'active';
    }

    /**
     * Chech is active state
     *
     * @return bool
     */
    public function isActiveState()
    {
        return true;
    }

    /**
     * Check is inactive state
     *
     * @return bool
     */
    public function isInactiveState()
    {
        return false;
    }

    /**
     * Check is pending state
     *
     * @return bool
     */
    public function isPendingState()
    {
        return false;
    }

    /**
     * Check is unreachable state
     *
     * @return bool
     */
    public function isUnreachable()
    {
        return false;
    }

    /**
     * Check is unreachable state (pass through)
     *
     * @return bool
     */
    public function isUnreachableState()
    {
        return $this->isUnreachable();
    }

    /**
     * @return int
     */
    public function getProposalAmount()
    {
        return 0;
    }

    /**
     * @return string
     */
    public function getEdition()
    {
        return 'opensource';
    }

    /**
     * @return string|null
     */
    public function getOrderId()
    {
        return null;
    }

    /**
     * @return int
     */
    public function getSimplifiedLicenseState()
    {
        return License::OPENSOURCE;
    }

    /**
     * @param string $edition
     * @return bool
     */
    public function isAllowedForEdition($edition = 'opensource')
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isTrial()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isMpVersion()
    {
        if ($this->getDomain() == 'magento.local') {
            return true;
        }

        if (strpos($this->getDomain(), 'magentosite.cloud') !== false) {
            return true;
        }

        return false;
    }
}
