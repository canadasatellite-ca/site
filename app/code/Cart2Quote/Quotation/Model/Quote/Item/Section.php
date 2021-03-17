<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Item;

use Magento\Framework\Model\AbstractExtensibleModel;
use Cart2Quote\Quotation\Api\Data\Quote\Item\SectionInterface;

/**
 * Class Section
 *
 * @package Cart2Quote\Quotation\Model\Quote\Item
 */
class Section extends AbstractExtensibleModel implements SectionInterface
{
    use \Cart2Quote\Features\Traits\Model\Quote\Item\Section {
        getSectionId as private traitGetSectionId;
        getSectionItemId as private traitGetSectionItemId;
        getItemId as private traitGetItemId;
        getSortOrder as private traitGetSortOrder;
        setSectionId as private traitSetSectionId;
        setItemId as private traitSetItemId;
        setSortOrder as private traitSetSortOrder;
        _construct as private _traitConstruct;
    }

    /**
     * Get section id
     *
     * @return int
     */
    public function getSectionId()
    {
        return $this->traitGetSectionId();
    }

    /**
     * Get section item id
     *
     * @return int
     */
    public function getSectionItemId()
    {
        return $this->traitGetSectionItemId();
    }

    /**
     * Get item id
     *
     * @return int
     */
    public function getItemId()
    {
        return $this->traitGetItemId();
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
     * Get section id
     *
     * @param int $sectionId
     * @return $this
     */
    public function setSectionId($sectionId)
    {
        return $this->traitSetSectionId($sectionId);
    }

    /**
     * Set item id
     *
     * @param int $itemId
     * @return $this
     */
    public function setItemId($itemId)
    {
        return $this->traitSetItemId($itemId);
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
}
