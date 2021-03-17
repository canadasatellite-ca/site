<?php

namespace BroSolutions\IssueNotification\Block\Adminhtml\Checkout\Edit;

/**
 * Class SaveButton
 * @package BroSolutions\IssueNotification\Block\Adminhtml\Checkout\Edit
 */
class SaveButton extends GenericButton implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}
