<?php

namespace Interactivated\Quotecheckout\Block\Adminhtml\System\Config;

class ResetConfig extends \Magento\Config\Block\System\Config\Form\Field
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Interactivated_Quotecheckout::system/config/button.phtml');
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }

    public function getAjaxCheckUrl()
    {
        return $this->getUrl('onestepcheckout/onestepcheckout/check');
    }

    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(
                [
                    'id' => 'onestepcheckout_button',
                    'label' => __('Reset To Default Config')
                ]
            );

        return $button->toHtml();
    }
}
