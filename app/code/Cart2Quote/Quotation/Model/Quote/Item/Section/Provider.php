<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Item\Section;

/**
 * Class Provider
 *
 * @package Cart2Quote\Quotation\Model\Quote\Section
 */
class Provider implements \Cart2Quote\Quotation\Api\Quote\Item\SectionProviderInterface
{
    use \Cart2Quote\Features\Traits\Model\Quote\Item\Section\Provider {
        getSection as private traitGetSection;
    }

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Item\SectionFactory
     */
    private $sectionFactory;
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section
     */
    private $sectionResourceModel;
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section\Collection
     */
    private $sectionItemCollection;

    /**
     * Provider constructor.
     *
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section\Collection $sectionItemCollection
     * @param \Cart2Quote\Quotation\Model\Quote\Item\SectionFactory $sectionFactory
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section $sectionResourceModel
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section\Collection $sectionItemCollection,
        \Cart2Quote\Quotation\Model\Quote\Item\SectionFactory $sectionFactory,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section $sectionResourceModel
    ) {
        $this->sectionFactory = $sectionFactory;
        $this->sectionResourceModel = $sectionResourceModel;
        $this->sectionItemCollection = $sectionItemCollection;
    }

    /**
     * Get section for with a given item id
     *
     * @param int $itemId
     * @return \Cart2Quote\Quotation\Api\Data\Quote\Item\SectionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSection($itemId)
    {
        return $this->traitGetSection($itemId);
    }
}
