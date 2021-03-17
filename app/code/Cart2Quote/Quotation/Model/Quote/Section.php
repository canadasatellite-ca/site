<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote;

use Magento\Framework\Model\AbstractExtensibleModel;
use Cart2Quote\Quotation\Api\Data\Quote\SectionInterface;

/**
 * Class Section
 *
 * @package Cart2Quote\Quotation\Model\Quote
 */
class Section extends AbstractExtensibleModel implements SectionInterface
{
    use \Cart2Quote\Features\Traits\Model\Quote\Section {
        getSectionId as private traitGetSectionId;
        getQuoteId as private traitGetQuoteId;
        getLabel as private traitGetLabel;
        getSortOrder as private traitGetSortOrder;
        setSectionId as private traitSetSectionId;
        setQuoteId as private traitSetQuoteId;
        setLabel as private traitSetLabel;
        setSortOrder as private traitSetSortOrder;
        _construct as private _traitConstruct;
        getIsUnassigned as private traitGetIsUnassigned;
        setIsUnassigned as private traitSetIsUnassigned;
    }

    /**
     * Get sections id
     *
     * @return int
     */
    public function getSectionId()
    {
        return $this->traitGetSectionId();
    }

    /**
     * Get quote id
     *
     * @return int
     */
    public function getQuoteId()
    {
        return $this->traitGetQuoteId();
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->traitGetLabel();
    }

    /**
     * Get sort order
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->traitGetSortOrder();
    }

    /**
     * Set sections id
     *
     * @param int $sectionId
     * @return $this
     */
    public function setSectionId($sectionId)
    {
        return $this->traitSetSectionId($sectionId);
    }

    /**
     * Set quote id
     *
     * @param int $quoteId
     * @return $this
     */
    public function setQuoteId($quoteId)
    {
        return $this->traitSetQuoteId($quoteId);
    }

    /**
     * Set label
     *
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        return $this->traitSetLabel($label);
    }

    /**
     * Set sort order
     *
     * @param string $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder)
    {
        return $this->traitSetSortOrder($sortOrder);
    }

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_traitConstruct();
    }

    /**
     * @return bool
     */
    public function getIsUnassigned()
    {
        return $this->traitGetIsUnassigned();
    }

    /**
     * @param bool $isUnassigned
     * @return $this
     */
    public function setIsUnassigned($isUnassigned)
    {
        return $this->traitSetIsUnassigned($isUnassigned);
    }
}
