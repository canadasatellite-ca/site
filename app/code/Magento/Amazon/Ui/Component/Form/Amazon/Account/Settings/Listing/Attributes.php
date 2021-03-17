<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Account\Settings\Listing;

use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\Framework\Api\MetadataObjectInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Attributes
 */
class Attributes implements OptionSourceInterface
{
    /** @var Repository $productAttributeRepository */
    protected $productAttributeRepository;

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
    public function toOptionArray()
    {
        /** @var array */
        $productAttributeArray = [];
        /** @var array */
        $data = [];
        /** @var MetadataObjectInterface */
        $list = $this->productAttributeRepository->getCustomAttributesMetadata();

        foreach ($list as $attribute) {
            /** @var string */
            $attributeCode = $attribute->getAttributeCode();

            if (in_array($attributeCode, Definitions::ATTRIBUTE_EXCLUSION)) {
                continue;
            }

            if ($attribute->getBackendType() == 'varchar') {
                $productAttributeArray[$attributeCode] = $attribute->getFrontendLabel();
            }
        }

        $productAttributeArray['sku'] = 'SKU';

        ksort($productAttributeArray);
        $productAttributeArray = ['0' => 'Not Selected'] + $productAttributeArray;

        foreach ($productAttributeArray as $key => $value) {
            $data[] = ['value' => $key, 'label' => __($value)];
        }

        return $data;
    }
}
