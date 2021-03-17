<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Amazon\Attribute\Value;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;

/**
 * Class View
 */
class View extends Container
{
    /** @var string */
    protected $_template = 'amazon/attribute/value/view.phtml';
    /**
     * @var \Magento\Amazon\Ui\FrontendUrl
     */
    private $frontendUrl;

    /**
     * @param Context $context
     * @param \Magento\Amazon\Ui\FrontendUrl $frontendUrl
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Amazon\Ui\FrontendUrl $frontendUrl,
        array $data = []
    ) {
        $this->frontendUrl = $frontendUrl;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_amazon_attribute_value';
        $this->_mode = 'view';
        $this->_blockGroup = 'Magento_Amazon';
        $this->setData('id', 'channel_amazon_attribute_value_index');

        /** @var string */
        $backUrl = $this->frontendUrl->getHomeUrl('/attributes');

        $this->buttonList->update('save', 'label', __('Save attribute settings'));
        $this->buttonList->update('save', 'class', 'spectrumButton');
        $this->buttonList->update('back', 'onclick', 'setLocation(\'' . $backUrl . '\')');
        $this->buttonList->update('back', 'class', 'spectrumButton spectrumButton--secondary back');
        $this->buttonList->remove('delete');
        $this->buttonList->remove('reset');
    }

    /**
     * @return string
     */
    public function getSwitchUrl()
    {
        if ($url = $this->getData('switch_url')) {
            return $url;
        }
        return $this->getUrl('adminhtml/*/*', ['_current' => true, 'period' => null]);
    }
}
