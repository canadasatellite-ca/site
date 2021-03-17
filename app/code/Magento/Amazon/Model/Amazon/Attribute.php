<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon;

use Magento\Amazon\Api\Data\AttributeInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Attribute
 *
 */
class Attribute extends AbstractModel implements AttributeInterface
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\Amazon\Model\ResourceModel\Amazon\Attribute::class
        );
    }

    /**
     * Get attribute id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * Get assigned attribute amazon attribute code
     *
     * @return string|null
     */
    public function getAmazonAttribute()
    {
        return $this->getData('amazon_attribute');
    }

    /**
     * Set assigned attribute amazon attribute code
     *
     * @param string $code
     * @return $this
     */
    public function setAmazonAttribute($code)
    {
        return $this->setData('amazon_attribute', $code);
    }

    /**
     * Get assigned attribute catalog attribute code
     *
     * @return string|null
     */
    public function getCatalogAttribute()
    {
        return $this->getData('catalog_attribute');
    }

    /**
     * Set assigned attribute catalog attribute code
     *
     * @param string $code
     * @return $this
     */
    public function setCatalogAttribute($code)
    {
        return $this->setData('catalog_attribute', $code);
    }

    /**
     * Get assigned attribute country code
     *
     * @return string|null
     */
    public function getCountryCode()
    {
        return $this->getData('country_code');
    }

    /**
     * Set assigned attribute country code
     *
     * @param string $value
     * @return void
     */
    public function setCountryCode(string $value)
    {
        $this->setData('country_code', $value);
    }

    /**
     * Get overwrite flag
     *
     * @return bool|null
     */
    public function getOverwrite()
    {
        return $this->getData('overwrite');
    }

    /**
     * Set overwrite flag
     *
     * @param bool $flag
     * @return $this
     */
    public function setOverwrite($flag)
    {
        return $this->setData('overwrite', $flag);
    }

    /**
     * Get attribute type
     *
     * @return int|null
     */
    public function getType()
    {
        return $this->getData('type');
    }

    /**
     * Set attribute type
     *
     * @param int $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->setData('type', $type);
    }

    /**
     * Get assigned attribute set ids
     *
     * @return string|null
     */
    public function getAttributeSetIds()
    {
        return $this->getData('attribute_set_ids');
    }

    /**
     * Set assigned attribute set ids
     *
     * @param string $value
     * @return $this
     */
    public function setAttributeSetIds($value)
    {
        return $this->setData('attribute_set_ids', $value);
    }

    /**
     * Get in search flag
     *
     * @return int|null
     */
    public function getInSearch()
    {
        return $this->getData('in_search');
    }

    /**
     * Set in search flag
     *
     * @param int $value
     * @return $this
     */
    public function setInSearch($value)
    {
        return $this->setData('in_search', $value);
    }

    /**
     * Get comparable on frontend flag
     *
     * @return int|null
     */
    public function getComparable()
    {
        return $this->getData('comparable');
    }

    /**
     * Set comparable on frontend flag
     *
     * @param int $value
     * @return $this
     */
    public function setComparable($value)
    {
        return $this->setData('comparable', $value);
    }

    /**
     * Get in navigation flag
     *
     * @return int|null
     */
    public function getInNavigation()
    {
        return $this->getData('in_navigation');
    }

    /**
     * Set in navigation flag
     *
     * @param int $value
     * @return $this
     */
    public function setInNavigation($value)
    {
        return $this->setData('in_navigation', $value);
    }

    /**
     * Get in search navigation flag
     *
     * @return int|null
     */
    public function getInSearchNavigation()
    {
        return $this->getData('in_search_navigation');
    }

    /**
     * Set in search navigation flag
     *
     * @param int $value
     * @return $this
     */
    public function setInSearchNavigation($value)
    {
        return $this->setData('in_search_navigation', $value);
    }

    /**
     * Get position setting
     *
     * @return int|null
     */
    public function getPosition()
    {
        return $this->getData('position');
    }

    /**
     * Set position setting
     *
     * @param int $value
     * @return $this
     */
    public function setPosition($value)
    {
        return $this->setData('position', $value);
    }

    /**
     * Get in promo flag
     *
     * @return int|null
     */
    public function getInPromo()
    {
        return $this->getData('in_promo');
    }

    /**
     * Set in promo flag
     *
     * @param int $value
     * @return $this
     */
    public function setInPromo($value)
    {
        return $this->setData('in_promo', $value);
    }

    /**
     * Get is active flag
     *
     * @return int|null
     */
    public function getIsActive()
    {
        return $this->getData('is_active');
    }

    /**
     * Set is active flag
     *
     * @param int $value
     * @return $this
     */
    public function setIsActive($value)
    {
        return $this->setData('is_active', $value);
    }
}
