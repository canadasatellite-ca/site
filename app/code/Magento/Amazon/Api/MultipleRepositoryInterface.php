<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api;

use Magento\Amazon\Api\Data\MultipleInterface;

/**
 * Interface MultipleRepositoryInterface
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface MultipleRepositoryInterface
{
    /**
     * Get multiple by id
     *
     * @param int $id
     * @return MultipleInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Get multiples by parent id
     *
     * @param int $parentId
     * @return MultipleInterface[]
     */
    public function getByParentId($parentId);
}
