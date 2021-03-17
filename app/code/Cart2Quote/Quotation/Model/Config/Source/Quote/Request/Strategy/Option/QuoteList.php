<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Config\Source\Quote\Request\Strategy\Option;

/**
 * Class QuoteList
 *
 * @package Cart2Quote\Quotation\Model\Config\Source\Quote\Request\Strategy\Option
 */
class QuoteList implements \Cart2Quote\Quotation\Model\Config\Source\Form\Field\Select\OptionInterface
{
    use \Cart2Quote\Features\Traits\Model\Config\Source\Quote\Request\Strategy\Option\QuoteList {
        getLabel as private traitGetLabel;
        getComment as private traitGetComment;
    }

    /**
     * Get label
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getLabel()
    {
        return $this->traitGetLabel();
    }

    /**
     * Get comment
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getComment()
    {
        return $this->traitGetComment();
    }
}
