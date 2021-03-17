<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */


/**
 * Quotation Email quote items
 */

namespace Cart2Quote\Quotation\Block\Quote\Email;

/**
 * Class Items
 *
 * @package Cart2Quote\Quotation\Block\Quote\Email
 */
class Items extends \Magento\Sales\Block\Items\AbstractItems
{
    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * @var \Magento\GiftMessage\Helper\Message
     */
    protected $giftMessageHelper;

    /**
     * Items constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param \Magento\GiftMessage\Helper\Message $giftMessageHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Magento\GiftMessage\Helper\Message $giftMessageHelper,
        array $data = []
    ) {
        $this->quotationHelper = $quotationHelper;
        $this->giftMessageHelper = $giftMessageHelper;
        parent::__construct($context, $data);
    }

    /**
     * Check disabled product comment field
     *
     * @return boolean
     */
    public function isProductRemarkDisabled()
    {
        return $this->quotationHelper->isProductRemarkDisabled();
    }

    /**
     * Get sections form the quote
     *
     * @return array
     */
    public function getSections()
    {
        return $this->getQuote()->getSections();
    }

    /**
     * Check hide item price in request email configuration
     *
     * @return boolean
     */
    public function hidePrice()
    {
        return $this->quotationHelper->isHideEmailRequestPrice();
    }

    /**
     * Getter for the gift message helper
     *
     * @return \Magento\GiftMessage\Helper\Message
     */
    public function getGiftMessageHelper()
    {
        return $this->giftMessageHelper;
    }
}
