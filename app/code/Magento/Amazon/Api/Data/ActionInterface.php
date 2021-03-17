<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api\Data;

/**
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface ActionInterface
{
    /**
     * Get action id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get merchant id
     *
     * @return string|null
     */
    public function getMerchantId();

    /**
     * Set merchant id
     *
     * @param string $id
     * @return $this
     */
    public function setMerchantId($id);

    /**
     * Get unique identifier
     *
     * @return string|null
     */
    public function getIdentifier();

    /**
     * Set unique identifier
     *
     * @param string $identifier
     * @return $this
     */
    public function setIdentifier($identifier);
}
