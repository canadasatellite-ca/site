<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\ResourceModel\Amazon\Listing\Override;

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

        // filter on overrides only
        $this->addFieldToFilter(
            ['condition_override', 'list_price_override', 'handling_override', 'condition_notes_override'],
            [
                ['neq' => '0'],
                ['neq' => 'NULL'],
                ['neq' => 'NULL'],
                ['neq' => 'NULL']
            ]
        );

        $this->getSelect()->joinLeft(
            ['account' => $this->getTable('channel_amazon_account')],
            'main_table.merchant_id = account.merchant_id',
            ['account_name' => 'account.name']
        );

        $this->addFilterToMap('merchant_id', 'main_table.merchant_id');
        $this->addFilterToMap('name', 'main_table.name');
    }
}
