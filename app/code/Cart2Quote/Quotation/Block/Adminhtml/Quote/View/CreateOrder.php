<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View;

/**
 * Adminhtml quotation quote create order button
 */
class CreateOrder extends \Magento\Backend\Block\Widget
{
    /**
     * @var string
     */
    protected $_onClickCode;

    /**
     * Get buttons html
     *
     * @return string
     */
    public function getButtonsHtml()
    {
        $addButtonData = [
            'label' => __('Create Order'),
            'onclick' => $this->_onClickCode,
            'class' => 'action-add primary create-order'
        ];
        return $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        )->setData(
            $addButtonData
        )->toHtml();
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        //set the javascript for the onClick buttons
        $this->_onClickCode = ' jQuery("#edit_form").attr("action","' . $this->getSaveUrl() . '");
                                quote.submit();';

        //construct this Block
        parent::_construct();
        $this->setId('quotation_quote_view_create_order');
    }

    /**
     * Retrieve url for form submitting
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('quotation/quote/convert');
    }
}
