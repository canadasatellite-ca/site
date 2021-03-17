<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Section;
/**
 * Trait Provider
 *
 * @package Cart2Quote\Quotation\Model\Quote\Section
 */
trait Provider
{
    /**
     * Get all sections for a given quote id
     *
     * @param int $quoteId
     * @return \Cart2Quote\Quotation\Api\Data\Quote\SectionInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getSections($quoteId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$sections = [];
        $ids = $this->sectionCollection->getSectionIdsForQuote($quoteId);
        foreach ($ids as $id) {
            $sections[] = $this->getSection($id);
        }
        return $sections;
		}
	}
    /**
     * Get section by id
     *
     * @param int $sectionId
     * @return \Cart2Quote\Quotation\Model\Quote\Section
     */
    private function getSection($sectionId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$section = $this->sectionFactory->create();
        $this->sectionResourceModel->load($section, $sectionId);
        return $section;
		}
	}
}
