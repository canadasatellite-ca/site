<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api\Data;

/**
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface VariantInterface
{
    /**
     * Get variant id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get variant parent id
     *
     * @return int|null
     */
    public function getParentId();

    /**
     * Set variant parent id
     *
     * @param int $parentId
     * @return $this
     */
    public function setParentId($parentId);

    /**
     * Get variant asin
     *
     * @return string|null
     */
    public function getAsin();

    /**
     * Set variant asin
     *
     * @param string $asin
     * @return $this
     */
    public function setAsin($asin);

    /**
     * Get variant name
     *
     * @return string|null
     */
    public function getVariantName();

    /**
     * Set variant name
     *
     * @param string $name
     * @return $this
     */
    public function setVariantName($name);

    /**
     * Get variant value
     *
     * @return string|null
     */
    public function getVariantValue();

    /**
     * Set variant value
     *
     * @param string $value
     * @return $this
     */
    public function setVariantValue($value);
}
