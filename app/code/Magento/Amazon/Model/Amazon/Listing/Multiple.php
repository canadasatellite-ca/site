<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon\Listing;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Multiple
 */
class Multiple extends AbstractModel
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\Amazon\Model\ResourceModel\Amazon\Listing\Multiple::class
        );
    }

    /**
     * Get multiple match id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * Get multiple match parent id
     *
     * @return int|null
     */
    public function getParentId()
    {
        return $this->getData('parent_id');
    }

    /**
     * Set multiple match parent id
     *
     * @param int $parentId
     * @return $this
     */
    public function setParentId($parentId)
    {
        return $this->setData('parent_id', $parentId);
    }

    /**
     * Get multiple match asin
     *
     * @return string|null
     */
    public function getAsin()
    {
        return $this->getData('asin');
    }

    /**
     * Set multiple match asin
     *
     * @param string $asin
     * @return $this
     */
    public function setAsin($asin)
    {
        return $this->setData('asin', $asin);
    }

    /**
     * Get multiple match title
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->getData('title');
    }

    /**
     * Set multiple match title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        return $this->setData('title', $title);
    }

    /**
     * Get multiple match product type
     *
     * @return string|null
     */
    public function getProductType()
    {
        return $this->getData('product_type');
    }

    /**
     * Set multiple match product type
     *
     * @param string $type
     * @return $this
     */
    public function setProductType($type)
    {
        return $this->setData('product_type', $type);
    }
}
