<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Rule;

use Magento\Catalog\Model\ProductFactory;

/**
 * Class RuleOverride
 */
class AbstractProduct
{
    /** @var ProductFactory */
    protected $productFactory;

    /**
     * @param ProductFactory $productFactory
     */
    public function __construct(
        ProductFactory $productFactory
    ) {
        $this->productFactory = $productFactory;
    }

    /**
     * Adds a $flag argument to the method, and if set to true,
     * it checks the product attributes to ensure they still exist
     * before processing the validation
     *
     * @param Magento\Rule\Model\Condition\Product\AbstractProduct $subject
     * @param callable $proceed
     * @param bool $flag
     * @return array
     */
    public function aroundCollectValidatedAttributes(
        \Magento\Rule\Model\Condition\Product\AbstractProduct $subject,
        callable $proceed,
        $productCollection,
        $flag = false
    ) {
        $attribute = $subject->getAttribute();

        if (!$flag) {
            return $proceed($productCollection);
        }

        $product = $this->productFactory->create();
        if (!$product->getResource()->getAttribute($attribute)) {
            return $productCollection;
        }

        return $proceed($productCollection);
    }
}
