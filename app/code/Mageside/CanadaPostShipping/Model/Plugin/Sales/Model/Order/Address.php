<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Plugin\Sales\Model\Order;

use Mageside\CanadaPostShipping\Model\Plugin\Quote\Model\Quote\Address\CustomAttributeList;

class Address
{
    /**
     * @param \Magento\Sales\Model\Order\Address $subject
     * @param callable $proceed
     * @param $attributeCode
     * @param $attributeValue
     * @return \Magento\Sales\Model\Order\Address
     */
    public function aroundSetCustomAttribute(
        \Magento\Sales\Model\Order\Address $subject,
        callable $proceed,
        $attributeCode,
        $attributeValue
    ) {
        /** @var \Magento\Sales\Model\Order\Address $address */
        $address = $proceed($attributeCode, $attributeValue);
        $attributes = CustomAttributeList::CUSTOM_ATTRIBUTES;
        if (in_array($attributeCode, $attributes)) {
            $address->setData($attributeCode, $attributeValue);
        }

        return $address;
    }
}
