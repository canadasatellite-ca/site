<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Amazon\Account\Listing\Thirdparty\Create;

use Magento\Backend\Block\Widget\Form\Container;

/**
 * Class View
 */
class View extends Container
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_amazon_account_listing_thirdparty_create';
        $this->_mode = 'view';
        $this->_blockGroup = 'Magento_Amazon';
        $this->setData('id', 'channel_amazon_account_listing_thirdparty_create');

        $this->buttonList->remove('delete');
        $this->buttonList->remove('reset');

        /** @var int */
        $merchantId = $this->getRequest()->getParam('merchant_id');
        /** @var string */
        $previousTab = $this->getRequest()->getParam('tab');
        $backUrl = $this->getUrl(
            'channel/amazon/account_listing_index',
            ['merchant_id' => $merchantId, 'active_tab' => $previousTab]
        );

        $this->buttonList->update('back', 'onclick', 'setLocation(\'' . $backUrl . '\')');
        $this->buttonList->update('back', 'class', 'spectrumButton spectrumButton--secondary back');
        $this->buttonList->update('save', 'label', __('Create catalog products'));
        $this->buttonList->update('save', 'class', 'spectrumButton');
    }

    /**
     * Retrieve text for header element
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Create New Catalog Products');
    }
}
