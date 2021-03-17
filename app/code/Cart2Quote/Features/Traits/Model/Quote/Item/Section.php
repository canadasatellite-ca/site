<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Item;
use Magento\Framework\Model\AbstractExtensibleModel;
use Cart2Quote\Quotation\Api\Data\Quote\Item\SectionInterface;
/**
 * Trait Section
 *
 * @package Cart2Quote\Quotation\Model\Quote\Item
 */
trait Section
{
    /**
     * Get section id
     *
     * @return int
     */
    private function getSectionId()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\Quote\Item\SectionInterface::SECTION_ID);
		}
	}
    /**
     * Get section item id
     *
     * @return int
     */
    private function getSectionItemId()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\Quote\Item\SectionInterface::SECTION_ITEM_ID);
		}
	}
    /**
     * Get item id
     *
     * @return int
     */
    private function getItemId()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\Quote\Item\SectionInterface::ITEM_ID);
		}
	}
    /**
     * Get sort order
     *
     * @return int
     */
    private function getSortOrder()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\Quote\Item\SectionInterface::SORT_ORDER);
		}
	}
    /**
     * Get section id
     *
     * @param int $sectionId
     * @return $this
     */
    private function setSectionId($sectionId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(\Cart2Quote\Quotation\Api\Data\Quote\Item\SectionInterface::SECTION_ID, $sectionId);
        return $this;
		}
	}
    /**
     * Set item id
     *
     * @param int $itemId
     * @return $this
     */
    private function setItemId($itemId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(\Cart2Quote\Quotation\Api\Data\Quote\Item\SectionInterface::ITEM_ID, $itemId);
        return $this;
		}
	}
    /**
     * Set sort order
     *
     * @param string $sortOrder
     * @return $this
     */
    private function setSortOrder($sortOrder)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(\Cart2Quote\Quotation\Api\Data\Quote\Item\SectionInterface::SORT_ORDER, $sortOrder);
        return $this;
		}
	}
    /**
     * Init resource model
     *
     * @return void
     */
    private function _construct()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_init(\Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section::class);
		}
	}
}
