<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon\Attribute;

use Magento\Amazon\Api\Data\AttributeValueInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Value
 *
 */
class Value extends AbstractModel implements AttributeValueInterface
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\Amazon\Model\ResourceModel\Amazon\Attribute\Value::class
        );
    }

    /**
     * Get amazon attribute value id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * Get amazon attribute parent id
     *
     * @return int|null
     */
    public function getParentId()
    {
        return $this->getData('parent_id');
    }

    /**
     * Set amazon attribute parent id
     *
     * @param int $id
     * @return $this
     */
    public function setParentId($id)
    {
        return $this->setData('parent_id', $id);
    }

    /**
     * Get amazon attribute sku
     *
     * @return string|null
     */
    public function getSku()
    {
        return $this->getData('sku');
    }

    /**
     * Set amazon attribute sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku)
    {
        return $this->setData('sku', $sku);
    }

    /**
     * Get amazon attribute name
     *
     * @return string|null
     */
    public function getAmazonAttribute()
    {
        return $this->getData('amazon_attribute');
    }

    /**
     * Set amazon attribute name
     *
     * @param string $name
     * @return $this
     */
    public function setAmazonAttribute($name)
    {
        return $this->setData('amazon_attribute', $name);
    }

    /**
     * Get amazon attribute asin
     *
     * @return string|null
     */
    public function getAsin()
    {
        return $this->getData('asin');
    }

    /**
     * Set amazon attribute asin
     *
     * @param string $asin
     * @return $this
     */
    public function setAsin($asin)
    {
        return $this->setData('asin', $asin);
    }

    /**
     * Get amazon attribute value country code
     *
     * @return string|null
     */
    public function getCountryCode()
    {
        return $this->getData('country_code');
    }

    /**
     * Set amazon attribute value country code
     *
     * @param string $countryCode
     * @return void
     */
    public function setCountryCode(string $countryCode)
    {
        $this->setData('country_code', $countryCode);
    }

    /**
     * Get amazon attribute value
     *
     * @return string|null
     */
    public function getValue()
    {
        return $this->getData('value');
    }

    /**
     * Set amazon attribute value
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        return $this->setData('value', $value);
    }

    /**
     * Get amazon attribute value import status
     *
     * @return int|null
     */
    public function getStatus()
    {
        return $this->getData('status');
    }

    /**
     * Set amazon attribute value import status
     *
     * @param int $value
     * @return $this
     */
    public function setStatus($value)
    {
        return $this->setData('status', $value);
    }
}
