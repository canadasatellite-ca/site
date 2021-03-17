<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Account\Settings\Listing;

use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class PriceTypeField
 */
class PriceTypeField implements OptionSourceInterface
{
    /** @var CollectionFactory $collectionFactory */
    protected $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Returns option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var array */
        $attributeArray = [];
        /** @var array */
        $data = [];

        /** @var CollectionFactory */
        $attributeCollection = $this->collectionFactory->create();
        $attributeCollection->addFieldToFilter('entity_type_id', '4');
        $attributeCollection->addFieldToFilter('backend_type', 'decimal');
        $attributeCollection->addFieldToFilter('frontend_input', 'price');

        foreach ($attributeCollection as $attribute) {
            /** @var string */
            $attributeCode = $attribute->getAttributeCode();

            if (in_array($attributeCode, Definitions::ATTRIBUTE_EXCLUSION, true)) {
                continue;
            }

            $attributeArray[$attributeCode] = $attribute->getFrontendLabel();
        }

        ksort($attributeArray);

        foreach ($attributeArray as $key => $value) {
            $data[] = ['value' => $key, 'label' => __($value)];
        }

        return $data;
    }
}
