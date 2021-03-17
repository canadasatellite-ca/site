<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Section;

/**
 * Class Provider
 *
 * @package Cart2Quote\Quotation\Model\Quote\Section
 */
class Provider implements \Cart2Quote\Quotation\Api\Quote\SectionsProviderInterface
{
    use \Cart2Quote\Features\Traits\Model\Quote\Section\Provider {
        getSections as private traitGetSections;
        getSection as private traitGetSection;
    }

    /**
     * @var \Magento\Framework\EntityManager\EntityManager
     */
    private $entityManager;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\SectionFactory
     */
    private $sectionFactory;

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section
     */
    private $sectionResourceModel;

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section\Collection
     */
    private $sectionCollection;

    /**
     * Provider constructor.
     *
     * @param \Magento\Framework\EntityManager\EntityManager $entityManager
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section\Collection $sectionCollection
     * @param \Cart2Quote\Quotation\Model\Quote\SectionFactory $sectionFactory
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section $sectionResourceModel
     */
    public function __construct(
        \Magento\Framework\EntityManager\EntityManager $entityManager,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section\Collection $sectionCollection,
        \Cart2Quote\Quotation\Model\Quote\SectionFactory $sectionFactory,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section $sectionResourceModel
    ) {
        $this->entityManager = $entityManager;
        $this->sectionFactory = $sectionFactory;
        $this->sectionResourceModel = $sectionResourceModel;
        $this->sectionCollection = $sectionCollection;
    }

    /**
     * Get all sections for a given quote id
     *
     * @param int $quoteId
     * @return \Cart2Quote\Quotation\Api\Data\Quote\SectionInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSections($quoteId)
    {
        return $this->traitGetSections($quoteId);
    }

    /**
     * Get section by id
     *
     * @param int $sectionId
     * @return \Cart2Quote\Quotation\Model\Quote\Section
     */
    public function getSection($sectionId)
    {
        return $this->traitGetSection($sectionId);
    }
}
