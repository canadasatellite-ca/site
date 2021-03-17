<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Attribute\Value;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Api\StoreRepositoryInterface;

/**
 * Class AttributeSetIds
 */
class StoreIds implements OptionSourceInterface
{
    /** @var StoreRepositoryInterface $storeRepository */
    protected $storeRepository;

    /**
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        StoreRepositoryInterface $storeRepository
    ) {
        $this->storeRepository = $storeRepository;
    }

    /**
     * Creates the core attribute ids
     *
     * @return array
     */
    public function toOptionArray()
    {
        $stores = $this->storeRepository->getList();
        $storeList[0] = ['value' => 0, 'label' => "All Store Views (Global)"];
        foreach ($stores as $store) {
            $storeId = $store['store_id'];
            $storeName = $store['name'];

            if ($storeName == 'Admin') {
                continue;
            }

            $storeList[$storeId] = ['value' => $storeId, 'label' => $storeName];
        }
        sort($storeList);
        return $storeList;
    }
}
