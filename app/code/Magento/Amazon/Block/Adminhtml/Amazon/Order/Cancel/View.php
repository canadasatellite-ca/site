<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Amazon\Order\Cancel;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;

/**
 * Class View
 */
class View extends Container
{
    protected function _construct()
    {
        parent::_construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_amazon_order_cancel';
        $this->_mode = 'view';
        $this->_blockGroup = 'Magento_Amazon';
        $this->setData('id', 'channel_amazon_order_cancel_index');

        $this->buttonList->update('back', 'class', 'spectrumButton spectrumButton--secondary back');
        $this->buttonList->remove('delete');
        $this->buttonList->remove('reset');

        /** @var int */
        if ($orderId = $this->getRequest()->getParam('id')) {

            /** @var string */
            $backUrl = $this->getUrl('channel/amazon/order_details_index', ['id' => $orderId]);
            /** @var string */
            $saveUrl = $this->getUrl('channel/amazon/order_cancel_save', ['id' => $orderId]);

            $this->buttonList->update('back', 'onclick', 'setLocation(\'' . $backUrl . '\')');
            $this->buttonList->update('back', 'class', 'spectrumButton spectrumButton--secondary back');
            $this->buttonList->update('save', 'label', __('Cancel order'));
            $this->buttonList->update('save', 'onclick', 'setLocation(\'' . $saveUrl . '\')');
            $this->buttonList->update('save', 'class', 'spectrumButton');
        } else {
            $this->buttonList->remove('save');
            $this->buttonList->remove('back');
        }
    }

    /**
     * Retrieve text for header element
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Amazon Order Cancellation');
    }
}
