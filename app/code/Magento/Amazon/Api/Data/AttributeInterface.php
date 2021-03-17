<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api\Data;

/**
 * Class AttributeInterface
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface AttributeInterface
{
    /**
     * Get attribute id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get assigned attribute amazon attribute code
     *
     * @return string|null
     */
    public function getAmazonAttribute();

    /**
     * Set assigned attribute amazon attribute code
     *
     * @param string $code
     * @return $this
     */
    public function setAmazonAttribute($code);

    /**
     * Get assigned attribute catalog attribute code
     *
     * @return string|null
     */
    public function getCatalogAttribute();

    /**
     * Set assigned attribute catalog attribute code
     *
     * @param string $code
     * @return $this
     */
    public function setCatalogAttribute($code);

    /**
     * Get assigned attribute amazon country code
     *
     * @return string|null
     */
    public function getCountryCode();

    /**
     * Set assigned attribute amazon country code
     *
     * @param string $value
     * @return void
     */
    public function setCountryCode(string $value);

    /**
     * Get overwrite flag
     *
     * @return bool|null
     */
    public function getOverwrite();

    /**
     * Set overwrite flag
     *
     * @param bool $flag
     * @return $this
     */
    public function setOverwrite($flag);

    /**
     * Get attribute type
     *
     * @return int|null
     */
    public function getType();

    /**
     * Set attribute type
     *
     * @param int $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get assigned attribute set ids
     *
     * @return string|null
     */
    public function getAttributeSetIds();

    /**
     * Set assigned attribute set ids
     *
     * @param string $value
     * @return $this
     */
    public function setAttributeSetIds($value);

    /**
     * Get in search flag
     *
     * @return int|null
     */
    public function getInSearch();

    /**
     * Set in search flag
     *
     * @param int $value
     * @return $this
     */
    public function setInSearch($value);

    /**
     * Get comparable on frontend flag
     *
     * @return int|null
     */
    public function getComparable();

    /**
     * Set comparable on frontend flag
     *
     * @param int $value
     * @return $this
     */
    public function setComparable($value);

    /**
     * Get in navigation flag
     *
     * @return int|null
     */
    public function getInNavigation();

    /**
     * Set in navigation flag
     *
     * @param int $value
     * @return $this
     */
    public function setInNavigation($value);

    /**
     * Get in search navigation flag
     *
     * @return int|null
     */
    public function getInSearchNavigation();

    /**
     * Set in search navigation flag
     *
     * @param int $value
     * @return $this
     */
    public function setInSearchNavigation($value);

    /**
     * Get position setting
     *
     * @return int|null
     */
    public function getPosition();

    /**
     * Set position setting
     *
     * @param int $value
     * @return $this
     */
    public function setPosition($value);

    /**
     * Get in promo flag
     *
     * @return int|null
     */
    public function getInPromo();

    /**
     * Set in promo flag
     *
     * @param int $value
     * @return $this
     */
    public function setInPromo($value);

    /**
     * Get is active flag
     *
     * @return int|null
     */
    public function getIsActive();

    /**
     * Set is active flag
     *
     * @param int $value
     * @return $this
     */
    public function setIsActive($value);
}
