<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml;

/**
 * Class Quote
 *
 * Adminhtml quotation quotes block
 */
class Quote extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_quote';
        $this->_blockGroup = 'Cart2Quote_Quotation';
        $this->_headerText = __('Quotes');
        $this->_addButtonLabel = __('Create New Quote');
        parent::_construct();
        $this->addButton(
            'config',
            [
                'label' => __('Configure Quotation Management'),
                'class' => 'action-secondary',
                'onclick' => sprintf(
                    'setLocation(\'%s\')',
                    $this->getUrl('adminhtml/system_config/edit/section/cart2quote_quotation')),
            ],
            1
        );
    }

    /**
     * Retrieve url for order creation
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('quotation/quote_create/start');
    }
}
