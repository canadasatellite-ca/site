<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote;

/**
 * Class Create
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote
 */
class Create extends \Magento\Sales\Block\Adminhtml\Order\Create
{
    /**
     * Prepare header html
     *
     * @return string
     */
    public function getHeaderHtml()
    {
        $out = sprintf(
            '<div id="order-header">%s</div>',
            $this->getLayout()->createBlock(
                \Cart2Quote\Quotation\Block\Adminhtml\Quote\Create\Header::class
            )->toHtml()
        );

        return $out;
    }

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();

        //update save button
        $quoteId = $this->_getSession()->getQuotationQuoteId();
        $buttonText = $quoteId  ? 'Update Quote' : 'Create Quote';
        $this->buttonList->update('save', 'label', __($buttonText));
        //update back button
        $this->buttonList->update(
            'back',
            'onclick',
            "setLocation('". $this->getBackUrl() . "')"
        );

        //update cancel button
        $confirm = __('Are you sure you want to cancel this Quote?');

        $this->buttonList->update(
            'reset',
            'onclick',
            "deleteConfirm('$confirm','" . $this->getBackUrl() . "')"
        );
    }

    /**
     * Prepare layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $pageTitle = $this->getLayout()->createBlock(
            \Cart2Quote\Quotation\Block\Adminhtml\Quote\Create\Header::class
        )->toHtml();
        if (is_object($this->getLayout()->getBlock('page.title'))) {
            $this->getLayout()->getBlock('page.title')->setPageTitle($pageTitle);
        }
        return \Magento\Backend\Block\Widget\Form\Container::_prepareLayout();
    }

    /**
     * Get URL for back and cancel button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('quotation/quote/');
    }
}
