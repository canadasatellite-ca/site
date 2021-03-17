<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Section;
/**
 * Trait UnassignedCreator
 * @package Cart2Quote\Quotation\Model\Quote\Section
 */
trait UnassignedCreator
{
    /**
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return \Cart2Quote\Quotation\Model\Quote\Section
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    private function create(\Cart2Quote\Quotation\Model\Quote $quote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$unassignedSection = $this->sectionFactory->create();
        $unassignedSection->setQuoteId($quote->getId());
        $unassignedSection->setSortOrder(-1);
        $unassignedSection->setIsUnassigned(true);
        $this->sectionResourceModel->save($unassignedSection);
        return $unassignedSection;
		}
	}
}