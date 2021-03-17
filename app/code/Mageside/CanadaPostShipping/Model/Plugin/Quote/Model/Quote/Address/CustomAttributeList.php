<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Plugin\Quote\Model\Quote\Address;

class CustomAttributeList
{
    const CUSTOM_ATTRIBUTES = ['canada_dpo_id','extension_phone'];

    /**
     * @see \Magento\Quote\Model\Quote\Address\CustomAttributeList::getAttributes() return empty array
     *
     * @param \Magento\Quote\Model\Quote\Address\CustomAttributeList $subject
     * @param $result
     * @return array
     */
    public function afterGetAttributes(
        \Magento\Quote\Model\Quote\Address\CustomAttributeList $subject,
        $result
    ) {
        $attributes = array_combine(self::CUSTOM_ATTRIBUTES, self::CUSTOM_ATTRIBUTES);
        return array_merge($result, $attributes);
    }
}
