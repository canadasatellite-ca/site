<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote;
use Magento\Framework\Model\AbstractExtensibleModel;
use Cart2Quote\Quotation\Api\Data\Quote\SectionInterface;
/**
 * Trait Section
 *
 * @package Cart2Quote\Quotation\Model\Quote
 */
trait Section
{
    /**
     * Get sections id
     *
     * @return int
     */
    private function getSectionId()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\Quote\SectionInterface::SECTION_ID);
		}
	}
    /**
     * Get quote id
     *
     * @return int
     */
    private function getQuoteId()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\Quote\SectionInterface::QUOTE_ID);
		}
	}
    /**
     * Get label
     *
     * @return string
     */
    private function getLabel()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\Quote\SectionInterface::LABEL);
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
			return $this->getData(\Cart2Quote\Quotation\Api\Data\Quote\SectionInterface::SORT_ORDER);
		}
	}
    /**
     * Set sections id
     *
     * @param int $sectionId
     * @return $this
     */
    private function setSectionId($sectionId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(\Cart2Quote\Quotation\Api\Data\Quote\SectionInterface::SECTION_ID, $sectionId);
        return $this;
		}
	}
    /**
     * Set quote id
     *
     * @param int $quoteId
     * @return $this
     */
    private function setQuoteId($quoteId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(\Cart2Quote\Quotation\Api\Data\Quote\SectionInterface::QUOTE_ID, $quoteId);
        return $this;
		}
	}
    /**
     * Set label
     *
     * @param string $label
     * @return $this
     */
    private function setLabel($label)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(\Cart2Quote\Quotation\Api\Data\Quote\SectionInterface::LABEL, $label);
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
			$this->setData(\Cart2Quote\Quotation\Api\Data\Quote\SectionInterface::SORT_ORDER, $sortOrder);
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
			$this->_init(\Cart2Quote\Quotation\Model\ResourceModel\Quote\Section::class);
		}
	}
    /**
     * @return bool
     */
    private function getIsUnassigned()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\Quote\SectionInterface::IS_UNASSIGNED);
		}
	}
    /**
     * @param bool $isUnassigned
     * @return $this
     */
    private function setIsUnassigned($isUnassigned)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(\Cart2Quote\Quotation\Api\Data\Quote\SectionInterface::IS_UNASSIGNED, $isUnassigned);
        return $this;
		}
	}
}
