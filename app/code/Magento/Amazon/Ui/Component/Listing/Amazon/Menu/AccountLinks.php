<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Menu;

use Magento\Amazon\Model\ResourceModel\Amazon\Account\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class AccountLinks
 */
class AccountLinks implements OptionSourceInterface
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
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var array */
        $data = [];

        foreach ($this->collectionFactory->create() as $account) {
            array_push($data, ['value' => $account->getName(), 'label' => __($account->getName())]);
        }

        return $data;
    }
}
