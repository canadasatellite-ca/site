<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model;

use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\Framework\Api\MetadataObjectInterface;

class MagentoAttributes
{
    /** @var Repository $productAttributeRepository */
    private $productAttributeRepository;

    /**
     * @var array
     */
    private $attributes;

    /**
     * @param Repository $productAttributeRepository
     */
    public function __construct(
        Repository $productAttributeRepository
    ) {
        $this->productAttributeRepository = $productAttributeRepository;
    }

    /**
     * Get Magento product attribute options
     *
     * @return array
     */
    public function getAttributes(): array
    {
        if (null === $this->attributes) {
            $this->attributes = $this->getAttributesData();
        }

        return $this->attributes;
    }

    private function getAttributesData(): array
    {
        $productAttributeArray = [];
        $attributeArray = [];
        /** @var MetadataObjectInterface */
        $list = $this->productAttributeRepository->getCustomAttributesMetadata();

        foreach ($list as $attribute) {
            $attributeCode = $attribute->getAttributeCode();

            if (in_array($attributeCode, Definitions::ATTRIBUTE_EXCLUSION)) {
                continue;
            }

            if (!$attribute->getFrontendLabel()) {
                continue;
            }

            if ($attribute->getFrontendInput() == 'text') {
                $productAttributeArray[$attributeCode] = $attribute->getFrontendLabel();
            }

            if ($attribute->getFrontendInput() == 'select') {
                $productAttributeArray[$attributeCode] = $attribute->getFrontendLabel();
                $options = $attribute->getOptions();

                foreach ($options as $option) {
                    if ($option->getValue()) {
                        if ($option->getLabel()) {
                            $attributeArray[$attributeCode][$option->getValue()] =
                                $option->getLabel();
                        }
                    }
                }
            }
        }

        ksort($productAttributeArray);

        return $productAttributeArray;
    }
}
