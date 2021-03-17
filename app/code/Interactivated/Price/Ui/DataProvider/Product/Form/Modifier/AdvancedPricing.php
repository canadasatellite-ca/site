<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Interactivated\Price\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Catalog\Model\Locator\LocatorInterface;

/**
 * Customize Advanced Pricing modal panel
 */
class AdvancedPricing extends AbstractModifier
{
    const CODE_MSRP = 'msrp';
    const CODE_MSRP_DISPLAY_ACTUAL_PRICE_TYPE = 'msrp_display_actual_price_type';
    const CODE_ADVANCED_PRICING = 'advanced-pricing';
    const CODE_RECORD = 'record';

    const CODE_PRICE_TYPE = 'price_usd';
    const CODE_SPECIAL_PRICE_USD = 'special_price_usd';
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
        $groupCode = $this->getGroupCodeByField($meta, self::CODE_ADVANCED_PRICING);
        if ($groupCode) {
            $parentNode = &$meta[$groupCode]['children'][self::CODE_ADVANCED_PRICING]['children'];
            if (isset($parentNode['container_' . self::CODE_SPECIAL_PRICE_USD])) {
                $currentNode = &$parentNode['container_' . self::CODE_SPECIAL_PRICE_USD]['children'];
                $currentNode[self::CODE_SPECIAL_PRICE_USD]['arguments']['data']['config']['addbefore']
                    = "US$";
            }
            if (isset($parentNode['container_' . self::CODE_PRICE_TYPE])) {
                $currentNode = &$parentNode['container_' . self::CODE_PRICE_TYPE]['children'];
                $currentNode[self::CODE_PRICE_TYPE]['arguments']['data']['config']['addbefore']
                    = "US$";
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
