<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Account\Listing\Thirdparty\Create;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\System\Store;

/**
 * Class WebsiteIds
 */
class WebsiteIds implements OptionSourceInterface
{
    /** @var Store $store */
    protected $store;

    /**
     * @param Store $store
     */
    public function __construct(
        Store $store
    ) {
        $this->store = $store;
    }

    /**
     * Creates the core website ids
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var array */
        $websites = $this->store->getWebsiteValuesForForm();
        /** @var array */
        $websiteArray = [];

        foreach ($websites as $website) {
            $websiteArray[] = ['value' => $website['value'], 'label' => __($website['label'])];
        }

        return $websiteArray;
    }
}
