<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 * @codingStandardsIgnoreFile
 */

namespace Cart2Quote\Quotation\Api\Quote\Item;

/**
 * Interface SectionProviderInterface
 *
 * @package Cart2Quote\Quotation\Api\Quote\Item
 */
interface SectionProviderInterface
{
    /**
     * Get section
     *
     * @param int $itemId
     * @return \Cart2Quote\Quotation\Api\Data\Quote\Item\SectionInterface
     */
    public function getSection($itemId);
}
