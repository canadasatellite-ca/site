<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Api\Data;

/**
 * @deprecated
 */
interface AccountInterface
{
    /**
     * Get account id
     *
     * @return int|null
     */
    public function getMerchantId();

    /**
     * Set merchant id
     *
     * @param int $merchantId
     * @return int|null
     */
    public function setMerchantId($merchantId);

    /**
     * Get authentication status
     * @return int|null
     */
    public function getAuthenticationStatus();

    /**
     * Set authentication status
     *
     * @param string $authenticationStatus
     * @return $this
     */
    public function setAuthenticationStatus(string $authenticationStatus);

    /**
     * Get is active
     *
     * @return int|null
     */
    public function getIsActive();

    /**
     * Set is active
     *
     * @param int $flag
     * @return $this
     */
    public function setIsActive($flag);

    /**
     * Get seller id
     *
     * @return string|null
     */
    public function getSellerId();

    /**
     * Set seller id
     *
     * @param string $id
     * @return $this
     */
    public function setSellerId($id);

    /**
     * Get country code
     *
     * @return string|null
     */
    public function getCountryCode();

    /**
     * Set country code
     *
     * @param string $countryCode
     * @return void
     */
    public function setCountryCode(string $countryCode);

    /**
     * Get account name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Set account name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get email
     *
     * @return string|null
     */
    public function getEmail();

    /**
     * Set email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email);

    /**
     * Get base URL
     *
     * @return string|null
     */
    public function getBaseUrl();

    /**
     * Set base url
     *
     * @param string $url
     * @return $this
     */
    public function setBaseUrl($url);

    /**
     * Get consumer key
     *
     * @return string|null
     */
    public function getConsumerKey();

    /**
     * Set consumer key
     *
     * @param string $key
     * @return $this
     */
    public function setConsumerKey($key);

    /**
     * Get consumer secret
     *
     * @return string|null
     */
    public function getConsumerSecret();

    /**
     * Set consumer secret
     *
     * @param string $secret
     * @return $this
     */
    public function setConsumerSecret($secret);

    /**
     * Get access token
     *
     * @return string|null
     */
    public function getAccessToken();

    /**
     * Set access token
     *
     * @param string $token
     * @return $this
     */
    public function setAccessToken($token);

    /**
     * Get access secret
     *
     * @return string|null
     */
    public function getAccessSecret();

    /**
     * Set access secret
     *
     * @param string $secret
     * @return $this
     */
    public function setAccessSecret($secret);

    /**
     * Get uuid
     *
     * @return string|null
     */
    public function getUuid();

    /**
     * Set uuid
     *
     * @param string $uuid
     * @return void
     */
    public function setUuid($uuid);

    /**
     * @return bool
     */
    public function getReportRun(): bool;

    /**
     * @param bool $reportRun
     * @return void
     */
    public function setReportRun(bool $reportRun);
}
