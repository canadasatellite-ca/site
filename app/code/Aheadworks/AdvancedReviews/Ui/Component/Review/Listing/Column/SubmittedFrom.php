<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\Component\Review\Listing\Column;

use Magento\Store\Ui\Component\Listing\Column\Store;
use Magento\Store\Model\Store as StoreModel;

/**
 * Class SubmittedFrom
 * @package Aheadworks\AdvancedReviews\Ui\Component\Review\Listing\Column
 */
class SubmittedFrom extends Store
{
    /**
     * {@inheritdoc}
     */
    protected function prepareItem(array $item)
    {
        $cellContent = parent::prepareItem($item);

        if ($item[$this->storeKey] == StoreModel::DEFAULT_STORE_ID) {
            $cellContent = $this->getAdminStoreName();
        }

        return $cellContent;
    }

    /**
     * Retrieve admin store name
     *
     * @return string
     */
    private function getAdminStoreName()
    {
        return $this->storeManager->getStore(StoreModel::DEFAULT_STORE_ID)->getName();
    }
}
