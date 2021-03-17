<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Item;
/**
 * Trait Updater
 *
 * @package Cart2Quote\Quotation\Model\Quote\Item
 */
trait Updater
{
    /**
     * Update
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param array $info
     * @return \Magento\Quote\Model\Quote\Item\Updater
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    private function update(\Magento\Quote\Model\Quote\Item $item, array $info)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (isset($info['item_has_comment'])) {
            $item->setDescription($info['description']);
        } else {
            $item->setDescription(null);
        }
        if (isset($info['section_id']) && !empty($info['section_id'])) {
            if (!in_array($info['section_id'], $this->sectionCollection->getSectionIdsForQuote($item->getQuoteId()))) {
                $info['section_id'] = null;
            }
            $item->getExtensionAttributes()->getSection()->setSectionId($info['section_id']);
            if (isset($info['sort_order']) && !empty($info['section_id'])) {
                $item->getExtensionAttributes()->getSection()->setSortOrder($info['sort_order']);
            }
            $this->sectionResourceModel->save($item->getExtensionAttributes()->getSection());
        } else {
            $this->sectionResourceModel->delete($item->getExtensionAttributes()->getSection());
        }
        return parent::update($item, $info);
		}
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
    private function parentConstruct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Framework\DataObject\Factory $objectFactory,
        $serializer
    ) {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$productMetadata = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\App\ProductMetadataInterface::class);
        $version = $productMetadata->getVersion();
        if (version_compare($version, "2.2.0", "<")) {
            parent::__construct($productFactory, $localeFormat, $objectFactory);
        } else {
            parent::__construct($productFactory, $localeFormat, $objectFactory, $serializer);
        }
		}
	}
}
