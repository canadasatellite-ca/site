<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon\Listing;

use Magento\Amazon\Api\Data\VariantInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Variant
 */
class Variant extends AbstractModel implements VariantInterface
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\Amazon\Model\ResourceModel\Amazon\Listing\Variant::class
        );
    }

    /**
     * Get variant id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * Get variant parent id
     *
     * @return int|null
     */
    public function getParentId()
    {
        return $this->getData('parent_id');
    }

    /**
     * Set variant parent id
     *
     * @param int $parentId
     * @return $this
     */
    public function setParentId($parentId)
    {
        return $this->setData('parent_id', $parentId);
    }

    /**
     * Get variant asin
     *
     * @return string|null
     */
    public function getAsin()
    {
        return $this->getData('asin');
    }

    /**
     * Set variant asin
     *
     * @param string $asin
     * @return $this
     */
    public function setAsin($asin)
    {
        return $this->setData('asin', $asin);
    }

    /**
     * Get variant name
     *
     * @return string|null
     */
    public function getVariantName()
    {
        return $this->getData('variant_name');
    }

    /**
     * Set variant name
     *
     * @param string $name
     * @return $this
     */
    public function setVariantName($name)
    {
        return $this->setData('variant_name', $name);
    }

    /**
     * Get variant value
     *
     * @return string|null
     */
    public function getVariantValue()
    {
        return $this->getData('variant_value');
    }

    /**
     * Set variant value
     *
     * @param string $value
     * @return $this
     */
    public function setVariantValue($value)
    {
        return $this->setData('variant_value', $value);
    }
}
