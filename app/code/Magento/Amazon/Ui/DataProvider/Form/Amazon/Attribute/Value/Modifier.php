<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\DataProvider\Form\Amazon\Attribute\Value;

use Magento\Amazon\Api\AttributeRepositoryInterface;
use Magento\Amazon\Api\Data\AttributeInterface;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Class Modifier
 */
class Modifier implements ModifierInterface
{
    /** @var Http $request */
    protected $request;
    /** @var AttributeRepositoryInterface $attributeRepository */
    protected $attributeRepository;
    /** @var ProductFactory $productFactory */
    protected $productFactory;

    /**
     * @param Http $request
     * @param AttributeRepositoryInterface $attributeRepository
     * @param ProductFactory $productFactory
     */
    public function __construct(
        Http $request,
        AttributeRepositoryInterface $attributeRepository,
        ProductFactory $productFactory
    ) {
        $this->request = $request;
        $this->attributeRepository = $attributeRepository;
        $this->productFactory = $productFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        /** @var int */
        $id = $this->request->getParam('id');

        $meta = $this->prepareTooltipLinks($meta);

        try {
            /** @var AttributeInterface */
            $amazonAttribute = $this->attributeRepository->getById($id);
        } catch (NoSuchEntityException $e) {
            return $meta;
        }

        /** @var string */
        if (!$attributeCode = $amazonAttribute->getCatalogAttribute()) {
            $meta = array_replace_recursive(
                $meta,
                [
                    'channel_amazon_attributes' => [
                        'children' => [
                            'scope_fieldset' => [
                                'children' => [
                                    'store_ids' => [
                                        'arguments' => [
                                            'data' => [
                                                'config' => [
                                                    'visible' => false
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            );
            return $meta;
        }

        if ($this->isAttributeGlobal($attributeCode)) {
            $meta = array_replace_recursive(
                $meta,
                [
                    'channel_amazon_attributes' => [
                        'children' => [
                            'scope_fieldset' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'visible' => false
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            );
            return $meta;
        }

        $meta = array_replace_recursive(
            $meta,
            [
                'channel_amazon_attributes' => [
                    'children' => [
                        'scope_fieldset' => [
                            'children' => [
                                'is_global' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'disabled' => true
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        return $meta;
    }

    /**
     * Prepare tooltip links
     *
     * @param array $meta
     * @return array
     */
    private function prepareTooltipLinks(array $meta)
    {
        $catalogAttributeTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_CATALOG_ATTRIBUTE
        ];

        $meta = array_replace_recursive(
            $meta,
            [
                'channel_amazon_attributes' => [
                    'children' => [
                        'catalog_attribute' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/catalog-attribute',
                                        'tooltip' => $catalogAttributeTooltip
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        return $meta;
    }

    /**
     * Returns the is_global value by attribute code
     *
     * @param string $attributeCode
     * @return bool
     */
    private function isAttributeGlobal($attributeCode)
    {
        /** @var ProductFactory */
        $product = $this->productFactory->create();

        if (!$attribute = $product->getResource()->getAttribute($attributeCode)) {
            return true;
        }

        return $attribute->getIsGlobal();
    }
}
