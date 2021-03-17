<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 * @codingStandardsIgnoreFile
 */

namespace Cart2Quote\Quotation\Api\Quote;

/**
 * Interface SectionsProviderInterface
 *
 * @package Cart2Quote\Quotation\Api\Quote
 */
interface SectionsProviderInterface
{
    /**
     * Get sections
     *
     * @param int $quoteId
     * @return \Cart2Quote\Quotation\Api\Data\Quote\SectionInterface[]
     */
    public function getSections($quoteId);
}
