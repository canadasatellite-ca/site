<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 * @codingStandardsIgnoreFile
 */

namespace Cart2Quote\Quotation\Api\Data\Quote;

/**
 * Interface SectionInterface
 *
 * @package Cart2Quote\Quotation\Api\Data\Quote
 */
interface SectionInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Section Id identifier
     */
    const SECTION_ID = "section_id";
    /**
     * Quote Id identifier
     */
    const QUOTE_ID = "quote_id";
    /**
     * Label Identifier
     */
    const LABEL = "label";
    /**
     * Sort Order identifier
     */
    const SORT_ORDER = "sort_order";
    /**
     * is unassigned section identifier
     */
    const IS_UNASSIGNED = 'is_unassigned';

    /**
     * Get section id
     *
     * @return int
     */
    public function getSectionId();

    /**
     * Get quote id
     *
     * @return int
     */
    public function getQuoteId();

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel();

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
     * Set quote id
     *
     * @param int $quoteId
     * @return self
     */
    public function setQuoteId($quoteId);

    /**
     * Set label
     *
     * @param string $label
     * @return self
     */
    public function setLabel($label);

    /**
     * Set sort order
     *
     * @param string $sortOrder
     * @return self
     */
    public function setSortOrder($sortOrder);

    /**
     * Get is unassigned section
     *
     * @return boolean
     */
    public function getIsUnassigned();

    /**
     * @param boolean $isUnassigned
     * @return $this
     */
    public function setIsUnassigned($isUnassigned);
}
