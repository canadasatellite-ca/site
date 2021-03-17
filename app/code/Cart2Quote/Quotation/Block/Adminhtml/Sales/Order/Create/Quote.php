<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Sales\Order\Create;

/**
 * Class Quote
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\Sales\Order\Create
 */
class Quote extends \Magento\Sales\Block\Adminhtml\Order\Create
{
    /**
     * Add Button to Sales Order menu
     * hide during store selector
     */
    protected function _construct()
    {
        parent::_construct();
        $createForNew = $this->getUrl('quotation/quote_create');
        $createForExisting = $this->getUrl('quotation/sales_order_create/quote');
        $customerId = $this->_sessionQuote->getCustomerId();
        $storeId = $this->_sessionQuote->getStoreId();

        $this->addButton(
            'create-quote',
            [
                'label' => __('Create Quote'),
                'class' => 'save secondary',
                'onclick' => sprintf('setLocation("%s")', $createForNew),

                'data_attribute' =>
                    ['mage-init' => ['createQuote' => '']]
            ]
        );
        if ($customerId && $customerId > 0) {
            $this->buttonList->update(
                'create-quote',
                'onclick',
                sprintf(
                    'setLocation("%s")',
                    $createForExisting
                )
            );
        }
        if (!$storeId) {
            $this->buttonList->update('create-quote', 'style', 'display:none');
        }
    }
}
