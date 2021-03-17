<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model\Amazon;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Account
 *
 */
class Account extends AbstractModel implements AccountInterface
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\Amazon\Model\ResourceModel\Amazon\Account::class
        );
    }

    /**
     * Get merchant id
     *
     * @return int|null
     */
    public function getMerchantId()
    {
        return $this->getData('merchant_id');
    }

    /**
     * Set merchant id
     *
     * @return int|null
     */
    public function setMerchantId($merchantId)
    {
        return $this->setData('merchant_id', $merchantId);
    }

    /**
     * Get setup step
     *
     * @return string|null
     */
    public function getAuthenticationStatus()
    {
        return $this->getData('authentication_status');
    }

    /**
     * Set setup step
     *
     * @param string $authenticationStatus
     * @return $this
     */
    public function setAuthenticationStatus(string $authenticationStatus)
    {
        return $this->setData('authentication_status', $authenticationStatus);
    }

    /**
     * Get is active
     *
     * @return int|null
     */
    public function getIsActive()
    {
        $isActive = $this->getData('is_active');
        return $isActive === null ? null : (int) $isActive;
    }

    /**
     * Set is active
     *
     * @param int $flag
     * @return $this
     */
    public function setIsActive($flag)
    {
        return $this->setData('is_active', $flag);
    }

    /**
     * Get seller id
     *
     * @return string|null
     */
    public function getSellerId()
    {
        return $this->getData('seller_id');
    }

    /**
     * Set seller id
     *
     * @param string $id
     * @return $this
     */
    public function setSellerId($id)
    {
        return $this->setData('seller_id', $id);
    }

    /**
     * Get country code
     *
     * @return string|null
     */
    public function getCountryCode()
    {
        return $this->getData('country_code');
    }

    /**
     * Set country code
     *
     * @param string $countryCode
     * @return void
     */
    public function setCountryCode(string $countryCode)
    {
        $this->setData('country_code', $countryCode);
    }

    /**
     * Get account name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->getData('name');
    }

    /**
     * Set account name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        return $this->setData('name', $name);
    }

    /**
     * Get email
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->getData('email');
    }

    /**
     * Set email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        return $this->setData('email', $email);
    }

    /**
     * Get base URL
     *
     * @return string|null
     */
    public function getBaseUrl()
    {
        return $this->getData('base_url');
    }

    /**
     * Set base url
     *
     * @param string $url
     * @return $this
     */
    public function setBaseUrl($url)
    {
        return $this->setData('base_url', $url);
    }

    /**
     * Get consumer key
     *
     * @return string|null
     */
    public function getConsumerKey()
    {
        return $this->getData('consumer_key');
    }

    /**
     * Set consumer key
     *
     * @param string $key
     * @return $this
     */
    public function setConsumerKey($key)
    {
        return $this->setData('consumer_key', $key);
    }

    /**
     * Get consumer secret
     *
     * @return string|null
     */
    public function getConsumerSecret()
    {
        return $this->getData('consumer_secret');
    }

    /**
     * Set consumer secret
     *
     * @param string $secret
     * @return $this
     */
    public function setConsumerSecret($secret)
    {
        return $this->setData('consumer_secret', $secret);
    }

    /**
     * Get access token
     *
     * @return string|null
     */
    public function getAccessToken()
    {
        return $this->getData('access_token');
    }

    /**
     * Set access token
     *
     * @param string $token
     * @return $this
     */
    public function setAccessToken($token)
    {
        return $this->setData('access_token', $token);
    }

    /**
     * Get access secret
     *
     * @return string|null
     */
    public function getAccessSecret()
    {
        return $this->getData('access_secret');
    }

    /**
     * Set access secret
     *
     * @param string $secret
     * @return $this
     */
    public function setAccessSecret($secret)
    {
        return $this->setData('access_secret', $secret);
    }

    /**
     * Get uuid
     *
     * @return string|null
     */
    public function getUuid()
    {
        return $this->getData('uuid');
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     * @return void
     */
    public function setUuid($uuid)
    {
        $this->setData('uuid', $uuid);
    }

    /**
     * @return bool
     */
    public function getReportRun(): bool
    {
        return (bool)$this->getData('report_run');
    }

    /**
     * @param bool $reportRun
     * @return void
     */
    public function setReportRun(bool $reportRun)
    {
        $this->setData('report_run', $reportRun);
    }
}
