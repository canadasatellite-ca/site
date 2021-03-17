<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\System\Config\Grid;

/**
 * Class AddressField
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\System\Config\Grid
 */
class AddressField extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'Cart2Quote_Quotation::system/config/grid/address.phtml';

    /**
     * Get grid configuration
     *
     * @return string
     */
    public function getAddressGridConfig()
    {
        return json_encode([
            'field' => [
                'name' => $this->getElement()->getName(),
                'label' => $this->getElement()->getLabel(),
                'htmlId' => $this->getElement()->getHtmlId(),
                'fieldValue' => $this->getElement()->getValue()
            ]
        ]);
    }

    /**
     * Get the button and scripts contents
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);

        return $this->toHtml();
    }
}
