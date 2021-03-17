<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View;

/**
 * Class InternalComment
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View
 */
class InternalComment extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\AbstractView
{
    /**
     * Data Form object
     *
     * @var \Magento\Framework\Data\Form
     */
    protected $_form;

    /**
     * Get header css class
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-comment';
    }

    /**
     * Get header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Internal Comment');
    }

    /**
     * Get comment note
     *
     * @return string
     */
    public function getInternalComment()
    {
        return $this->escapeHtml($this->getQuote()->getInternalComment());
    }
}
