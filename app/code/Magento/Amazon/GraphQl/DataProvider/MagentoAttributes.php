<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\DataProvider;

class MagentoAttributes
{
    /**
     * @var array
     */
    private $attributes;
    /**
     * @var \Magento\Amazon\Model\MagentoAttributes
     */
    private $magentoAttributes;

    /**
     * @param \Magento\Amazon\Model\MagentoAttributes $magentoAttributes
     */
    public function __construct(\Magento\Amazon\Model\MagentoAttributes $magentoAttributes)
    {
        $this->magentoAttributes = $magentoAttributes;
    }

    public function getMagentoAttributes(): array
    {
        if (null === $this->attributes) {
            $this->attributes = $this->getAttributesData();
        }
        return $this->attributes;
    }

    private function getAttributesData(): array
    {
        $productAttributeArray = $this->magentoAttributes->getAttributes();
        $data = [];
        foreach ($productAttributeArray as $key => $value) {
            $data[] = ['code' => $key, 'name' => $value];
        }

        return $data;
    }
}
