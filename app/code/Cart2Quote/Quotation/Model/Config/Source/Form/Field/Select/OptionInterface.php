<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Config\Source\Form\Field\Select;

/**
 * Interface OptionInterface
 *
 * @package Cart2Quote\Quotation\Model\Config\Source\Form\Field\Select
 */
interface OptionInterface
{
    /**
     * Get label
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getLabel();

    /**
     * Get comment
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getComment();
}
