<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 * @codingStandardsIgnoreFile
 */

namespace Cart2Quote\Quotation\Api\Data\Quote\Item;

/**
 * Interface SectionInterface
 *
 * @package Cart2Quote\Quotation\Api\Data\Quote\Item
 */
interface SectionInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const SECTION_ITEM_ID = 'section_item_id';
    /**
     *
     */
    const SECTION_ID = "section_id";
    /**
     *
     */
    const ITEM_ID = "item_id";
    /**
     *
     */
    const SORT_ORDER = "sort_order";

    /**
     * Get section item id
     *
     * @return int
     */
    public function getSectionItemId();

    /**
     * Get section id
     *
     * @return int
     */
    public function getSectionId();

    /**
     * Get item id
     *
     * @return int
     */
    public function getItemId();

    /**
     * Get sort order
     *
     * @return int
     */
    public function getSortOrder();

    /**
     * Set section id
     *
     * @param int $sectionId
     * @return self
     */
    public function setSectionId($sectionId);

    /**
     * Set item id
     *
     * @param int $itemId
     * @return self
     */
    public function setItemId($itemId);

    /**
     * Set sort order
     *
     * @param string $sortOrder
     * @return self
     */
    public function setSortOrder($sortOrder);
}
