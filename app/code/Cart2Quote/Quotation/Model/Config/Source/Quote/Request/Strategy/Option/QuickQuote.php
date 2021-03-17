<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Config\Source\Quote\Request\Strategy\Option;

/**
 * Class QuickQuote
 *
 * @package Cart2Quote\Quotation\Model\Config\Source\Quote\Request\Strategy\Option
 */
class QuickQuote implements \Cart2Quote\Quotation\Model\Config\Source\Form\Field\Select\OptionInterface
{
    use \Cart2Quote\Features\Traits\Model\Config\Source\Quote\Request\Strategy\Option\QuickQuote {
        getLabel as private traitGetLabel;
        getComment as private traitGetComment;
    }

    /**
     * Get Label
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getLabel()
    {
        return $this->traitGetLabel();
    }

    /**
     * Get Comment
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getComment()
    {
        return $this->traitGetComment();
    }
}
