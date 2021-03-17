<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Plugin\Customer\Model\Metadata;

use Mageside\CanadaPostShipping\Model\Plugin\Quote\Model\Quote\Address\CustomAttributeList;
use Magento\Customer\Api\AddressMetadataInterface;

class AddressMetadata
{
    /**
     * @param \Magento\Customer\Model\Metadata\AddressMetadata $subject
     * @param callable $proceed
     * @param $dataObjectClassName
     * @return mixed
     */
    public function aroundGetCustomAttributesMetadata(
        \Magento\Customer\Model\Metadata\AddressMetadata $subject,
        callable $proceed,
        $dataObjectClassName = AddressMetadataInterface::DATA_INTERFACE_NAME
    ) {
        /** @var \Magento\Sales\Model\Order\Address $address */
        $customAttributes = $proceed($dataObjectClassName);
        $customAttributeCodes = [];
        foreach ($customAttributes as $attribute) {
            $customAttributeCodes[] = $attribute->getAttributeCode();
        }

        $attributes = CustomAttributeList::CUSTOM_ATTRIBUTES;
        foreach ($subject->getAllAttributesMetadata() as $attributeMetadata) {
            $attributeCode = $attributeMetadata->getAttributeCode();
            if (in_array($attributeCode, $attributes) && !in_array($attributeCode, $customAttributeCodes)) {
                $customAttributes[] = $attributeMetadata;
            }
        }

        return $customAttributes;
    }
}
