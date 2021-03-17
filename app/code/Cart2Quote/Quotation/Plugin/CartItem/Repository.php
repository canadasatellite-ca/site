<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\CartItem;

/**
 * Class Repository
 *
 * @package Cart2Quote\Quotation\Plugin\CartItem
 */
class Repository
{
    /**
     * After save plugin
     *
     * @param \Magento\Quote\Api\Data\CartItemRepositoryInterface $subject
     * @param \Magento\Quote\Api\Data\CartItemInterface $entity
     * @return \Magento\Quote\Api\Data\CartItemInterface
     */
    public function afterSave(
        \Magento\Quote\Api\Data\CartItemRepositoryInterface $subject,
        \Magento\Quote\Api\Data\CartItemInterface $entity
    ) {

        return $entity;
    }

    /**
     * After get plugin
     *
     * @param \Magento\Quote\Api\Data\CartItemRepositoryInterface $subject
     * @param \Magento\Quote\Api\Data\CartItemInterface $entity
     * @return \Magento\Quote\Api\Data\CartItemInterface
     */
    public function afterGet(
        \Magento\Quote\Api\Data\CartItemRepositoryInterface $subject,
        \Magento\Quote\Api\Data\CartItemInterface $entity
    ) {

        return $entity;
    }
}
