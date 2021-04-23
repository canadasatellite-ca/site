<?php

namespace MageSuper\Casat\Model\Product\Attribute\Backend;

class Profit extends \Magento\Catalog\Model\Product\Attribute\Backend\Price
{
    /**
     * @param \Magento\Catalog\Model\Product $object
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    function validate($object)
    {
        $value = $object->getData($this->getAttribute()->getAttributeCode());
        if (empty($value)) {
            return parent::validate($object);
        }
        return true;
    }

}