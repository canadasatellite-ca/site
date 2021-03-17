<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Quote\Request\Form;

use Cart2Quote\Quotation\Model\Strategy\StrategyInterface;
use Cart2Quote\Quotation\Block\Product\Listing\Form as ListingForm;

/**
 * Class QuickQuote
 *
 * @package Cart2Quote\Quotation\Block\Quote\Request\Form
 */
class QuickQuote extends ListingForm implements StrategyInterface
{
    /**
     * @var string
     */
    protected $_template = "Cart2Quote_Quotation::product/list/quote/request/quickquote/form.phtml";

    /**
     * @var bool
     */
    private static $modalIsRendered = false;

    /**
     * Get Modal to HTML
     *
     * @param string $modalBlockName
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getModalHtml($modalBlockName)
    {
        if (!self::$modalIsRendered) {
            self::$modalIsRendered = true;

            return $this->getLayout()->getBlock($modalBlockName)->toHtml();
        }

        return '';
    }
}
