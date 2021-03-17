<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\ResourceModel\Amazon\Listing\Thirdparty;

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
            Definitions::THIRDPARTY_LIST_STATUS
        ];

        $this->getSelect()->joinLeft(
            ['account' => $this->getTable('channel_amazon_account')],
            'main_table.merchant_id = account.merchant_id',
            ['account_name' => 'account.name']
        );

        // filter on inactive products only
        $this->addFieldToFilter('list_status', Definitions::THIRDPARTY_LIST_STATUS);

        $this->addFilterToMap('merchant_id', 'main_table.merchant_id');
        $this->addFilterToMap('condition', 'main_table.condition');
    }
}
