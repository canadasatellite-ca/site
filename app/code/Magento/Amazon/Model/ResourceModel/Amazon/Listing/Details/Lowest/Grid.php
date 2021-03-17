<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\ResourceModel\Amazon\Listing\Details\Lowest;

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
        $this->addFieldToFilter('main_table.asin', ['notnull' => true]);

        $this->getSelect()->joinInner(
            ['account' => $this->getTable('channel_amazon_account')],
            'account.merchant_id = main_table.merchant_id',
            []
        )->joinLeft(
            ['lowest' => $this->getTable('channel_amazon_pricing_lowest')],
            'account.country_code = lowest.country_code AND lowest.asin = main_table.asin',
            [
                'id',
                'fulfillment_channel',
                'feedback_rating',
                'feedback_count',
                'condition_code',
                'list_price',
                'shipping_price',
                'landed_price',
                'last_updated'
            ]
        )->where(
            'lowest.asin IS NOT NULL'
        );

        parent::_initSelect();

        $this->addFilterToMap('id', 'main_table.id');
        $this->addFilterToMap('list_price', 'lowest.list_price');
        $this->addFilterToMap('shipping_price', 'lowest.shipping_price');
        $this->addFilterToMap('landed_price', 'lowest.landed_price');
    }
}
