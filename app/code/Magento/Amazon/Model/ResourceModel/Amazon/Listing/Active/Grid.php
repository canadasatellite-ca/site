<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\ResourceModel\Amazon\Listing\Active;

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
            Definitions::REMOVE_IN_PROGRESS_LIST_STATUS,
            Definitions::CONDITION_OVERRIDE_LIST_STATUS,
            Definitions::ACTIVE_LIST_STATUS
        ];

        $this->getSelect()->joinLeft(
            ['account' => $this->getTable('channel_amazon_account')],
            'main_table.merchant_id = account.merchant_id',
            ['account_name' => 'account.name']
        );

        $this->getSelect()->joinLeft(
            ['bbb' => $this->getTable('channel_amazon_pricing_bestbuybox')],
            'main_table.asin = bbb.asin AND bbb.country_code = account.country_code AND bbb.is_seller > 0',
            ['is_seller' => 'bbb.is_seller']
        );

        $this->getSelect()->group('main_table.id');

        // filter on active published products only
        $this->addFieldToFilter('list_status', ['in' => $statuses]);

        $this->addFilterToMap('merchant_id', 'main_table.merchant_id');
        $this->addFilterToMap('condition', 'main_table.condition');
        $this->addFilterToMap('name', 'main_table.name');
    }
}
