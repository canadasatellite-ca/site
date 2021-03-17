<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Plugin\Quote\Model;

use Mageside\CanadaPostShipping\Model\Plugin\Quote\Model\Quote\Address\CustomAttributeList;
use Magento\Framework\Model\AbstractExtensibleModel;

class Address
{
    /**
     * @see \Magento\Framework\Reflection\DataObjectProcessor::buildOutputDataArray()
     * in line $value = $dataObject->{$methodName}(); when $methodName == 'getCustomAttributes'
     * then @see \Magento\Framework\Model\AbstractExtensibleModel::getCustomAttributes() (remove keys)
     * @see \Magento\Framework\Model\AbstractExtensibleModel::setData()
     * then @see \Magento\Framework\Model\AbstractExtensibleModel::filterCustomAttributes()
     * in line $data[self::CUSTOM_ATTRIBUTES] = array_intersect_key() we lose our data, because keys didn't intersect
     *
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param $key
     * @param $value
     * @return array
     */
    public function beforeSetData(
        \Magento\Quote\Model\Quote\Address $address,
        $key,
        $value = null
    ) {
        if ($key == AbstractExtensibleModel::CUSTOM_ATTRIBUTES) {
            foreach ($value as $item) {
                $attrCode = ($item instanceof \Magento\Framework\Api\AttributeValue) ?
                    $item->getAttributeCode() :
                    $item['attribute_code'];
                $attrValue = ($item instanceof \Magento\Framework\Api\AttributeValue) ?
                    $item->getValue() :
                    $item['value'];
                if (in_array($attrCode, CustomAttributeList::CUSTOM_ATTRIBUTES)) {
                    $address->setData($attrCode, $attrValue);
                }
            }
        }

        return null;
    }
}
