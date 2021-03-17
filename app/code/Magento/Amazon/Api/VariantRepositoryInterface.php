<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api;

use Magento\Amazon\Api\Data\VariantInterface;

/**
 * Interface VariantRepositoryInterface
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface VariantRepositoryInterface
{
    /**
     * Get variant by id
     *
     * @param int $id
     * @return VariantInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Get variants by parent id
     *
     * @param int $parentId
     * @return VariantInterface[]
     */
    public function getByParentId($parentId);
}
