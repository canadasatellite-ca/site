<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Plugin\Customer\Api\Data;

use Mageside\CanadaPostShipping\Model\Plugin\Quote\Model\Quote\Address\CustomAttributeList;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\AttributeValue;

class AddressInterface
{
    /**
     * @var AttributeValueFactory
     */
    protected $_attributeValueFactory;

    /**
     * AddressInterface constructor.
     * @param AttributeValueFactory $attributeValueFactory
     */
    public function __construct(AttributeValueFactory $attributeValueFactory)
    {
        $this->_attributeValueFactory = $attributeValueFactory;
    }

    /**
     * @param \Magento\Customer\Api\Data\AddressInterface $subject
     * @param $result
     * @return mixed
     */
    public function afterGetCustomAttributes(
        \Magento\Customer\Api\Data\AddressInterface $subject,
        $result
    ) {
        foreach (CustomAttributeList::CUSTOM_ATTRIBUTES as $attributeCode) {
            if (empty($result[$attributeCode])) {
                /** @var AttributeValue $attribute */
                $attribute = $this->_attributeValueFactory->create();
                $attribute->setAttributeCode($attributeCode)
                    ->setValue('');
                $result[$attributeCode] = $attribute;
            }
        }

        return $result;
    }
}
