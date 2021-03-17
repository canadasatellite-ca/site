<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Account\Settings\Listing;

use Magento\Amazon\Model\MagentoAttributes;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class AttributeSource
 */
class AttributeSource implements OptionSourceInterface
{
    /**
     * @var MagentoAttributes
     */
    private $magentoAttributes;

    /**
     * @param MagentoAttributes $magentoAttributes
     */
    public function __construct(
        MagentoAttributes $magentoAttributes
    ) {
        $this->magentoAttributes = $magentoAttributes;
    }

    /**
     * Get Magento product attribute options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $productAttributeArray = $this->magentoAttributes->getAttributes();
        $data = [];
        foreach ($productAttributeArray as $key => $value) {
            $data[] = ['value' => $key, 'label' => __($value)];
        }

        return $data;
    }
}
