<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\License\Helper\Data;

/**
 * Class License
 * @package Cart2Quote\License\Helper\Data
 * @SuppressWarnings(PHPMD.FinalImplementation)
 */
final class License implements \Cart2Quote\Quotation\Helper\Data\LicenseInterface
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    private $backendUrl;

    /**
     * @var
     */
    private $simplifiedLicenseState;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $date;

    /**
     * License constructor
     */
    public function __construct()
    {
        $this->backendUrl = \Magento\Framework\App\ObjectManager::getInstance()->get(
            '\Magento\Backend\Model\UrlInterface'
        );

        $this->date = \Magento\Framework\App\ObjectManager::getInstance()->get(
            '\Magento\Framework\Stdlib\DateTime\TimezoneInterface'
        );
    }

    /**
     * Getter for the domain
     *
     * @return string
     */
    public function getDomain()
    {
        return \Cart2Quote\License\Model\License::getInstance()->getDomain();
    }

    /**
     * Getter for the edition
     *
     * @return string
     */
    public function getEdition()
    {
        return \Cart2Quote\License\Model\License::getInstance()->getEdition();
    }

    /**
     * Getter for the license state
     *
     * @return string
     */
    public function getLicenseState()
    {
        return \Cart2Quote\License\Model\License::getInstance()->getLicenseState();
    }

    /**
     * Is active state check
     *
     * @return bool
     */
    public function isActiveState()
    {
        return \Cart2Quote\License\Model\License::getInstance()->isActiveState();
    }

    /**
     * Is inactive state check
     *
     * @return bool
     */
    public function isInactiveState()
    {
        return \Cart2Quote\License\Model\License::getInstance()->isInactiveState();
    }

    /**
     * Is pending state check
     *
     * @return bool
     */
    public function isPendingState()
    {
        return \Cart2Quote\License\Model\License::getInstance()->isPendingState();
    }

    /**
     * Is unreachable state check (pass through)
     *
     * @return bool
     */
    public function isUnreachableState()
    {
        return $this->isUnreachable();
    }

    /**
     * Is unreachable state check
     *
     * @return bool
     */
    public function isUnreachable()
    {
        return \Cart2Quote\License\Model\License::getInstance()->isUnreachable();
    }

    /**
     * Getter for the proposal amount
     *
     * @return int
     */
    public function getProposalAmount()
    {
        return \Cart2Quote\License\Model\License::getInstance()->getProposalAmount();
    }

    /**
     * Getter for the license type
     *
     * @return string
     */
    public function getLicenseType()
    {
        return \Cart2Quote\License\Model\License::getInstance()->getLicenseType();
    }

    /**
     * Getter for the expiry date
     *
     * @return string|null
     */
    public function getExpiryDate()
    {
        return \Cart2Quote\License\Model\License::getInstance()->getExpiryDate();
    }

    /**
     * Getter for the order id
     *
     * @return string
     */
    public function getOrderId()
    {
        return \Cart2Quote\License\Model\License::getInstance()->getOrderId();
    }

    /**
     * Getter for the simplified license state
     *
     * @return int
     */
    public function getSimplifiedLicenseState()
    {
        if (!$this->simplifiedLicenseState) {
            if ($this->getEdition() == 'opensource') {
                return $this->simplifiedLicenseState = self::OPENSOURCE;

            }
            if ($this->isUnreachable()) {
                return $this->simplifiedLicenseState = self::SERVER_UNREACHABLE;
            }

            if ($this->getOrderId() !== null && !\is_numeric($this->getOrderId())) {
                return $this->simplifiedLicenseState = self::NOT_VALID;
            }
            if ($this->getLicenseType() == 'one_off') {
                $expiryDate = $this->getExpiryDate();
                $expiryDateObject = $this->date->date($expiryDate, null, false, true)->format('d-m-Y');
                $nowDateObject = $this->date->date(null, null, false, true)->format('d-m-Y');
                if ($this->isPendingState()) {
                    if ($this->getEdition() == 'trial' && $this->getOrderId() == null) {
                        return $this->simplifiedLicenseState = self::VALID_TRIAL;
                    } else {
                        return $this->simplifiedLicenseState = self::PENDING_LICENSE;
                    }
                }
                if ($this->isInactiveState() || \strtotime($nowDateObject) > \strtotime($expiryDateObject)) {
                    if ($this->getEdition() == 'trial') {
                        return $this->simplifiedLicenseState = self::EXPIRED_TRIAL;
                    }

                    return $this->simplifiedLicenseState = self::EXPIRED_LICENSE;
                }
                if ($this->isActiveState()) {
                    if ($this->getEdition() == 'trial') {
                        return $this->simplifiedLicenseState = self::VALID_TRIAL;
                    }

                    return $this->simplifiedLicenseState = self::VALID_LICENSE;
                }
            }

            if ($this->getLicenseType() != 'one_off') {
                if ($this->isActiveState()) {
                    return $this->simplifiedLicenseState = self::VALID_SUBSCRIPTION;
                }
                if ($this->isInactiveState()) {
                    return $this->simplifiedLicenseState = self::CANCELED_SUBSCRIPTION;
                }
            }

            return $this->simplifiedLicenseState = self::NOT_VALID;
        }

        return $this->simplifiedLicenseState;
    }

    /**
     * Is allowed for edition check
     *
     * @param string $edition
     * @return bool
     */
    public function isAllowedForEdition($edition = 'opensource')
    {
        return \Cart2Quote\License\Model\License::getInstance()->isAllowedForEdition($edition);
    }

    /**
     * Is trial check
     *
     * @return bool
     */
    public function isTrial()
    {
        return $this->getEdition() == 'trial';
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
