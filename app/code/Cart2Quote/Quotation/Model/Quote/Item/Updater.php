<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Item;

/**
 * Class Updater
 *
 * @package Cart2Quote\Quotation\Model\Quote\Item
 */
class Updater extends \Magento\Quote\Model\Quote\Item\Updater
{
    use \Cart2Quote\Features\Traits\Model\Quote\Item\Updater {
        update as private traitUpdate;
        parentConstruct as private traitParentConstruct;
    }

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section
     */
    private $sectionResourceModel;
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section\Collection
     */
    private $sectionCollection;

    /**
     * Updater constructor.
     *
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section\Collection $sectionCollection
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section $sectionResourceModel
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Framework\DataObject\Factory $objectFactory
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section\Collection $sectionCollection,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section $sectionResourceModel,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Framework\DataObject\Factory $objectFactory,
        $serializer = null
    ) {
        $this->parentConstruct($productFactory, $localeFormat, $objectFactory, $serializer);
        $this->sectionResourceModel = $sectionResourceModel;
        $this->sectionCollection = $sectionCollection;
    }

    /**
     * Update
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param array $info
     * @return \Magento\Quote\Model\Quote\Item\Updater
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function update(\Magento\Quote\Model\Quote\Item $item, array $info)
    {
        return $this->traitUpdate($item, $info);
    }

    /**
     * Magento updated the constructor with the serializer parameter in version 2.2.0
     * - this function is a fix for the error: "Extra parameters passed to parent construct: $serializer."
     *
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Framework\DataObject\Factory $objectFactory
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     */
    protected function parentConstruct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Framework\DataObject\Factory $objectFactory,
        $serializer
    ) {
        $this->traitParentConstruct($productFactory, $localeFormat, $objectFactory, $serializer);
    }
}
