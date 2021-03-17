<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api\Data;

/**
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface MultipleInterface
{
    /**
     * Get multiple match id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get multiple match parent id
     *
     * @return int|null
     */
    public function getParentId();

    /**
     * Set multiple match parent id
     *
     * @param int $parentId
     * @return $this
     */
    public function setParentId($parentId);

    /**
     * Get multiple match asin
     *
     * @return string|null
     */
    public function getAsin();

    /**
     * Set multiple match asin
     *
     * @param string $asin
     * @return $this
     */
    public function setAsin($asin);

    /**
     * Get multiple match title
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Set multiple match title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * Get multiple match product type
     *
     * @return string|null
     */
    public function getProductType();

    /**
     * Set multiple match product type
     *
     * @param string $type
     * @return $this
     */
    public function setProductType($type);
}
