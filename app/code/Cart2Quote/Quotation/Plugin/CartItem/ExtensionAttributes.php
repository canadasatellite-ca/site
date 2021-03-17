<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\CartItem;

/**
 * Class ExtensionAttributes
 *
 * @package Cart2Quote\Quotation\Plugin
 */
class ExtensionAttributes
{
    /**
     * @var \Magento\Quote\Api\Data\CartItemExtensionFactory
     */
    private $quoteItemExtensionFactory;
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Item\SectionFactory
     */
    private $sectionFactory;
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Item\Section\Provider
     */
    private $provider;

    /**
     * CartItemLoad constructor.
     *
     * @param \Cart2Quote\Quotation\Model\Quote\Item\Section\Provider $provider
     * @param \Cart2Quote\Quotation\Model\Quote\Item\SectionFactory $sectionFactory
     * @param \Magento\Quote\Api\Data\CartItemExtensionFactory $quoteItemExtensionFactory
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Quote\Item\Section\Provider $provider,
        \Cart2Quote\Quotation\Model\Quote\Item\SectionFactory $sectionFactory,
        \Magento\Quote\Api\Data\CartItemExtensionFactory $quoteItemExtensionFactory
    ) {
        $this->quoteItemExtensionFactory = $quoteItemExtensionFactory;
        $this->sectionFactory = $sectionFactory;
        $this->provider = $provider;
    }

    /**
     * After get extension attributes plugin
     *
     * @param \Magento\Quote\Api\Data\CartItemInterface $entity
     * @param \Magento\Quote\Api\Data\CartItemExtensionInterface|null $extension
     * @return \Magento\Quote\Api\Data\CartItemExtension|\Magento\Quote\Api\Data\CartItemExtensionInterface
     */
    public function afterGetExtensionAttributes(
        \Magento\Quote\Api\Data\CartItemInterface $entity,
        \Magento\Quote\Api\Data\CartItemExtensionInterface $extension = null
    ) {
        if ($extension === null) {
            $extension = $this->quoteItemExtensionFactory->create();
        }

        if ($extension->getSection() === null) {
            $extension->setSection($this->provider->getSection($entity->getItemId()));
            $entity->setExtensionAttributes($extension);
        }

        return $extension;
    }
}
