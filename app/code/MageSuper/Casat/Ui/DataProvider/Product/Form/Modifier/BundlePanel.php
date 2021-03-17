<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageSuper\Casat\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\UrlInterface;
use Magento\Bundle\Model\Product\Attribute\Source\Shipment\Type as ShipmentType;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form;
use Magento\Ui\Component\Modal;

/**
 * Create Ship Bundle Items and Affect Bundle Product Selections fields
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BundlePanel extends AbstractModifier
{
    const GROUP_CONTENT = 'content';
    const CODE_SHIPMENT_TYPE = 'shipment_type';
    const CODE_BUNDLE_DATA = 'bundle-items';
    const CODE_AFFECT_BUNDLE_PRODUCT_SELECTIONS = 'affect_bundle_product_selections';
    const CODE_BUNDLE_HEADER = 'bundle_header';
    const CODE_BUNDLE_OPTIONS = 'bundle_options';
    const SORT_ORDER = 20;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var ShipmentType
     */
    protected $shipmentType;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @param LocatorInterface $locator
     * @param UrlInterface $urlBuilder
     * @param ShipmentType $shipmentType
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        LocatorInterface $locator,
        UrlInterface $urlBuilder,
        ShipmentType $shipmentType,
        ArrayManager $arrayManager
    ) {
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
        $this->shipmentType = $shipmentType;
        $this->arrayManager = $arrayManager;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function modifyMeta(array $meta)
    {
        $path = $this->arrayManager->findPath(static::CODE_BUNDLE_DATA, $meta, null, 'children');

        $meta = $this->arrayManager->merge(
            $path,
            $meta,
            [
                'children' => [
                    self::CODE_BUNDLE_OPTIONS => $this->getBundleOptions()
                ]
            ]
        );

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }



    /**
     * Get Bundle Options structure
     *
     * @return array
     */
    protected function getBundleOptions()
    {
        return [
            'children' => [
                'record' => [
                    'children' => [
                        'product_bundle_container' => [
                            'children' => [
                                'bundle_selections' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'map' => [
                                                    'selection_price_value' => 'price'
                                                ],
                                            ],
                                        ],
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
