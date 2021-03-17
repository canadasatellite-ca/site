<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Checkout\Cart\Item\Renderer\Actions;

use Cart2Quote\Quotation\Helper\QuotationCart;
use Magento\Checkout\Helper\Cart;
use Magento\Framework\View\Element\Template;

/**
 * Class Remove
 *
 * @package Cart2Quote\Quotation\Block\Checkout\Cart\Item\Renderer\Actions
 */
class Remove extends \Magento\Checkout\Block\Cart\Item\Renderer\Actions\Generic
{
    /**
     * @var Cart
     */
    protected $cartHelper;

    /**
     * Remove constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Cart2Quote\Quotation\Helper\QuotationCart $cartHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        QuotationCart $cartHelper,
        array $data = []
    ) {
        $this->cartHelper = $cartHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get delete item POST JSON
     *
     * @return string
     */
    public function getDeletePostJson()
    {
        return $this->cartHelper->getDeletePostJson($this->getItem());
    }
}
