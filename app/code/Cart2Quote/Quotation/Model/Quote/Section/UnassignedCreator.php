<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Section;

/**
 * Class UnassignedCreator
 * @package Cart2Quote\Quotation\Model\Quote\Section
 */
class UnassignedCreator
{
    use \Cart2Quote\Features\Traits\Model\Quote\Section\UnassignedCreator {
        create as private traitCreate;
    }

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\SectionFactory
     */
    private $sectionFactory;
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section
     */
    private $sectionResourceModel;

    /**
     * Unassigned constructor.
     * @param \Cart2Quote\Quotation\Model\Quote\SectionFactory $sectionFactory
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section $sectionResourceModel
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Quote\SectionFactory $sectionFactory,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section $sectionResourceModel
    ) {
        $this->sectionFactory = $sectionFactory;
        $this->sectionResourceModel = $sectionResourceModel;
    }

    /**
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return \Cart2Quote\Quotation\Model\Quote\Section
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function create(\Cart2Quote\Quotation\Model\Quote $quote)
    {
        return $this->traitCreate($quote);
    }
}