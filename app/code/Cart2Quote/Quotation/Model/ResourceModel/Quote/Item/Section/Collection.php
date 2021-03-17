<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section;

/**
 * Class Collection
 * @package Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    use \Cart2Quote\Features\Traits\Model\ResourceModel\Quote\Item\Section\Collection {
        _construct as private _traitConstruct;
        getSectionIdForItem as private traitGetSectionIdForItem;
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_traitConstruct();
    }

    /**
     * @param string|int $itemId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSectionIdForItem($itemId)
    {
        return $this->traitGetSectionIdForItem($itemId);
    }
}