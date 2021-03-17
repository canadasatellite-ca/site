<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Item\Section;
/**
 * Trait Provider
 *
 * @package Cart2Quote\Quotation\Model\Quote\Section
 */
trait Provider
{
    /**
     * Get section for with a given item id
     *
     * @param int $itemId
     * @return \Cart2Quote\Quotation\Api\Data\Quote\Item\SectionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getSection($itemId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$section = $this->sectionFactory->create();
        $section->setItemId($itemId);
        $sectionId = $this->sectionItemCollection->getSectionIdForItem($itemId);
        $this->sectionResourceModel->load($section, $sectionId);
        return $section;
		}
	}
}
