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

class ProfitMarginAdvancedPricing extends AbstractModifier
{
    const CODE_ADVANCED_PRICING = 'advanced-pricing';

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
    function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
    }
    /**
     * {@inheritdoc}
     */
    function modifyMeta(array $meta)
    {
        $meta = $this->arrayManager->merge(
            $this->arrayManager->findPath('profit', $meta, null, 'children') . static::META_CONFIG_PATH,
            $meta,
            [
                'disabled' => true
            ]
        );
        $meta = $this->arrayManager->merge(
            $this->arrayManager->findPath('margin', $meta, null, 'children') . static::META_CONFIG_PATH,
            $meta,
            [
                'disabled' => true
            ]
        );

        $groupCode = $this->getGroupCodeByField($meta, self::CODE_ADVANCED_PRICING);
        if ($groupCode) {
            $parentNode = &$meta[$groupCode]['children'][self::CODE_ADVANCED_PRICING]['children'];
            if (isset($parentNode['container_margin' ])) {
                $currentNode = &$parentNode['container_margin']['children'];
                $currentNode['margin']['arguments']['data']['config']['addbefore']
                    = "%";
            }
        }
        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    function modifyData(array $data)
    {
        return $data;
    }
}
