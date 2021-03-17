<?php
namespace MageSuper\Casat\Block\Adminhtml\Order\Create\Shipping;

use Magento\Framework\Data\Form\Element\AbstractElement;
/****
 * Class Address
 * @package MageSuper\Casat\Block\Adminhtml\Order\Create\Shipping
 */
class Address extends \Magento\Sales\Block\Adminhtml\Order\Create\Shipping\Address
{
    /****
     * Canada Zip Code Validation
     * @param AbstractElement $element
     * @return $this
     */
    protected function _addAdditionalFormElementData(AbstractElement $element)
    {
        parent::_addAdditionalFormElementData($element);

        if ($element->getId() == 'postcode') {
            $element->setRequired(1);
            $element->setClass('canadian-postcode');
        }
        return $this;
    }
}