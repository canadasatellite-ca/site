<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Interactivated\Price\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\ObjectManagerInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Bundle\Model\Product\Type;
use Magento\Bundle\Api\ProductOptionRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class Bundle customizes Bundle product creation flow
 */
class Composite extends AbstractModifier
{
    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var array
     */
    protected $modifiers = [];

    /**
     * Object Manager
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var ProductOptionRepositoryInterface
     */
    protected $optionsRepository;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param LocatorInterface $locator
     * @param ObjectManagerInterface $objectManager
     * @param ProductOptionRepositoryInterface $optionsRepository
     * @param ProductRepositoryInterface $productRepository
     * @param array $modifiers
     */
    function __construct(
        LocatorInterface $locator,
        ObjectManagerInterface $objectManager,
        ProductOptionRepositoryInterface $optionsRepository,
        ProductRepositoryInterface $productRepository,
        array $modifiers = []
    ) {
        $this->locator = $locator;
        $this->objectManager = $objectManager;
        $this->optionsRepository = $optionsRepository;
        $this->productRepository = $productRepository;
        $this->modifiers = $modifiers;
    }

    /**
     * {@inheritdoc}
     */
    function modifyMeta(array $meta)
    {
        //if ($this->locator->getProduct()->getTypeId() === Type::TYPE_CODE) {
            foreach ($this->modifiers as $bundleClass) {
                /** @var ModifierInterface $bundleModifier */
                $bundleModifier = $this->objectManager->get($bundleClass);
                if (!$bundleModifier instanceof ModifierInterface) {
                    throw new \InvalidArgumentException(
                        'Type "' . $bundleClass . '" is not an instance of ' . ModifierInterface::class
                    );
                }
                $meta = $bundleModifier->modifyMeta($meta);
            }
        //}
        return $meta;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    function modifyData(array $data)
    {

        return $data;
    }
}
