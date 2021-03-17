<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Quote\Request\Button;

/**
 * Class QuickQuote
 *
 * @package Cart2Quote\Quotation\Block\Quote\Request\Button
 */
class QuickQuote extends Button implements \Cart2Quote\Quotation\Model\Strategy\StrategyInterface
{
    /**
     * @var string
     */
    protected $_template = "Cart2Quote_Quotation::product/view/quote/request/quickquote/button.phtml";

    /**
     * @var bool
     */
    private $modalIsRendered = false;

    /**
     * Get Modal to HTML
     *
     * @param string $modalBlockName
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getModalHtml($modalBlockName)
    {
        return $this->modalIsRendered ?: $this->getLayout()->getBlock($modalBlockName)->toHtml();
    }
}
