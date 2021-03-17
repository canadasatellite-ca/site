<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api\Data;

/**
 * Class AttributeValueInterface
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface AttributeValueInterface
{
    /**
     * Get amazon attribute value id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get amazon attribute parent id
     *
     * @return int|null
     */
    public function getParentId();

    /**
     * Set amazon attribute parent id
     *
     * @param int $id
     * @return $this
     */
    public function setParentId($id);

    /**
     * Get amazon attribute sku
     *
     * @return string|null
     */
    public function getSku();

    /**
     * Set amazon attribute sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku);

    /**
     * Get amazon attribute name
     *
     * @return string|null
     */
    public function getAmazonAttribute();

    /**
     * Set amazon attribute name
     *
     * @param string $name
     * @return $this
     */
    public function setAmazonAttribute($name);

    /**
     * Get amazon attribute asin
     *
     * @return string|null
     */
    public function getAsin();

    /**
     * Set amazon attribute asin
     *
     * @param string $asin
     * @return $this
     */
    public function setAsin($asin);

    /**
     * Get amazon attribute value country code
     *
     * @return string|null
     */
    public function getCountryCode();

    /**
     * Set amazon attribute value country code
     *
     * @param string $countryCode
     * @return void
     */
    public function setCountryCode(string $countryCode);

    /**
     * Get amazon attribute value
     *
     * @return string|null
     */
    public function getValue();

    /**
     * Set amazon attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value);

    /**
     * Get amazon attribute value import status
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Set amazon attribute value import status
     *
     * @param int $value
     * @return $this
     */
    public function setStatus($value);
}
