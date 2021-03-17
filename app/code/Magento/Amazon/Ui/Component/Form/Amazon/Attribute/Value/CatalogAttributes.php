<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Attribute\Value;

use Magento\Amazon\Api\AttributeRepositoryInterface;
use Magento\Amazon\Api\Data\AttributeInterface;
use Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\Framework\Api\MetadataObjectInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class CatalogAttributes
 */
class CatalogAttributes implements OptionSourceInterface
{
    /** @var array */
    const ALLOWED_FRONTEND_TYPES = [
        'select',
        'text'
    ];
    /** @var array */
    const ALLOWED_ATTRIBUTES = [
        'weight',
        'name',
        'short_description',
        'description'
    ];
    /** @var array */
    const IMAGE_TYPES = [
        'Thumbnail',
        'SmallImage',
        'LargeImage'
    ];

    /** @var Repository $productAttributeRepository */
    protected $productAttributeRepository;
    /** @var AttributeRepositoryInterface $attributeRepository */
    protected $attributeRepository;
    /** @var Http $request */
    protected $request;

    /**
     * @param Repository $productAttributeRepository
     * @param AttributeRepositoryInterface $attributeRepository
     * @param Http $request
     */
    public function __construct(
        Repository $productAttributeRepository,
        AttributeRepositoryInterface $attributeRepository,
        Http $request
    ) {
        $this->productAttributeRepository = $productAttributeRepository;
        $this->attributeRepository = $attributeRepository;
        $this->request = $request;
    }

    /**
     * Creates the attribute form field select list
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var array */
        $productAttributeArray = [];

        /** @var int */
        if (!$id = $this->request->getParam('id')) {
            return $productAttributeArray;
        }

        try {
            /** @var AttributeInterface */
            $amazonAttribute = $this->attributeRepository->getById($id);
        } catch (NoSuchEntityException $e) {
            return $productAttributeArray;
        }

        // special handling for image type
        if (in_array($amazonAttribute->getAmazonAttribute(), self::IMAGE_TYPES)) {

            /** @var MetadataObjectInterface */
            $list = $this->productAttributeRepository->getCustomAttributesMetadata();

            foreach ($list as $attribute) {
                if ($attribute->getData('frontend_input') == 'media_image') {
                    $productAttributeArray[] = [
                        'value' => $attribute->getData('attribute_code'),
                        'label' => __($attribute['frontend_label'])
                    ];
                }
            }

            ksort($productAttributeArray);
        } else {

            /** @var MetadataObjectInterface */
            $list = $this->productAttributeRepository->getCustomAttributesMetadata();

            foreach ($list as $attribute) {
                if ($attribute->getIsUserDefined()) {
                    if (in_array($attribute->getFrontendInput(), self::ALLOWED_FRONTEND_TYPES)) {
                        $productAttributeArray[] = [
                            'value' => $attribute->getData('attribute_code'),
                            'label' => __($attribute['frontend_label'])
                        ];
                    }
                } elseif (in_array($attribute->getAttributeCode(), self::ALLOWED_ATTRIBUTES)) {
                    $productAttributeArray[] = [
                        'value' => $attribute->getData('attribute_code'),
                        'label' => __($attribute['frontend_label'])
                    ];
                }
            }

            usort($productAttributeArray, function ($a, $b) {
                return strcmp($a['label'], __($b['label']));
            });

            array_unshift($productAttributeArray, ['value' => '0', 'label' => __('Create New Magento Attribute')]);
        }

        return $productAttributeArray;
    }
}
