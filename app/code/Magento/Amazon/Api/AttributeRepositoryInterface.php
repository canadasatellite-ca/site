<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api;

use Magento\Amazon\Api\Data\AttributeInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Interface AttributeRepositoryInterface
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface AttributeRepositoryInterface
{
    /**
     * Save attribute object
     *
     * @param AttributeInterface $account
     * @return AttributeInterface
     * @throws CouldNotSaveException
     */
    public function save(AttributeInterface $attribute);

    /**
     * Get attribute by id
     *
     * @param int $id
     * @return AttributeInterface
     */
    public function getById($id);
}
