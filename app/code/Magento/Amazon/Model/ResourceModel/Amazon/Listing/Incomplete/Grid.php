<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\ResourceModel\Amazon\Listing\Incomplete;

use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\AbstractListingGridCollection;

/**
 * Class Grid
 */
class Grid extends AbstractListingGridCollection
{
    /**
     * Add table join to primary grid table
     *
     * @return void
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        /** @var array */
        $statuses = [
            Definitions::MISSING_CONDITION_LIST_STATUS,
            Definitions::NOMATCH_LIST_STATUS,
            Definitions::MULTIPLE_LIST_STATUS,
            Definitions::VARIANTS_LIST_STATUS
        ];

        // filter on inactive products only
        $this->addFieldToFilter('list_status', ['in' => $statuses]);

        $this->getSelect()->joinLeft(
            ['account' => $this->getTable('channel_amazon_account')],
            'main_table.merchant_id = account.merchant_id',
            ['account_name' => 'account.name']
        );

        $this->addFilterToMap('merchant_id', 'main_table.merchant_id');
        $this->addFilterToMap('condition', 'main_table.condition');
        $this->addFilterToMap('name', 'main_table.name');
    }
}
