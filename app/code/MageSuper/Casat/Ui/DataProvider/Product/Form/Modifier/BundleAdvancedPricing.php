<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageSuper\Casat\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Catalog\Model\Locator\LocatorInterface;

/**
 * Customize Advanced Pricing modal panel
 */
class BundleAdvancedPricing extends AbstractModifier
{
    const CODE_MSRP = 'msrp';
    const CODE_MSRP_DISPLAY_ACTUAL_PRICE_TYPE = 'msrp_display_actual_price_type';
    const CODE_ADVANCED_PRICING = 'advanced-pricing';
    const CODE_RECORD = 'record';

    const CODE_PRICE_TYPE = 'price_type';
    const META_CONFIG_PATH = '/arguments/data/config';

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
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
    }
    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = $this->arrayManager->merge(
            $this->arrayManager->findPath(static::CODE_PRICE_TYPE, $meta, null, 'children') . static::META_CONFIG_PATH,
            $meta,
            [
                'disabled' => false
            ]
        );
        return $meta;

        $groupCode = $this->getGroupCodeByField($meta, self::CODE_ADVANCED_PRICING);
        if ($groupCode) {
            $parentNode = &$meta[$groupCode]['children'][self::CODE_ADVANCED_PRICING]['children'];
            if (isset($parentNode['container_' . self::CODE_MSRP])
                && isset($parentNode['container_' . self::CODE_MSRP_DISPLAY_ACTUAL_PRICE_TYPE])
            ) {
                unset($parentNode['container_' . self::CODE_MSRP]);
                unset($parentNode['container_' . self::CODE_MSRP_DISPLAY_ACTUAL_PRICE_TYPE]);
            }
            if (isset($parentNode['container_' . ProductAttributeInterface::CODE_SPECIAL_PRICE])) {
                $currentNode = &$parentNode['container_' . ProductAttributeInterface::CODE_SPECIAL_PRICE]['children'];
                $currentNode[ProductAttributeInterface::CODE_SPECIAL_PRICE]['arguments']['data']['config']['addbefore']
                    = "%";
            }
            $parentNodeChildren = &$parentNode[ProductAttributeInterface::CODE_TIER_PRICE]['children'];
            if (isset($parentNodeChildren[self::CODE_RECORD]['children'][ProductAttributeInterface::CODE_PRICE])) {
                $currentNode =
                &$parentNodeChildren[self::CODE_RECORD]['children'][ProductAttributeInterface::CODE_PRICE];
                $currentNode['arguments']['data']['config']['label'] = __('Percent Discount');
            }
        }

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }
}
