<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\ResourceModel\Amazon\Listing\Details\Bestbuybox;

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

        $this->getSelect()->joinLeft(
            ['account' => $this->getTable('channel_amazon_account')],
            'account.merchant_id = main_table.merchant_id',
            []
        )->joinLeft(
            ['bbb' => $this->getTable('channel_amazon_pricing_bestbuybox')],
            'account.country_code = bbb.country_code AND bbb.asin = main_table.asin',
            [
                'id',
                'is_seller',
                'country_code',
                'condition_code',
                'list_price',
                'shipping_price',
                'landed_price',
                'last_updated'
            ]
        )->where(
            'bbb.asin IS NOT NULL'
        );

        parent::_initSelect();

        $this->addFilterToMap('id', 'main_table.id');
        $this->addFilterToMap('list_price', 'bbb.list_price');
        $this->addFilterToMap('shipping_price', 'bbb.shipping_price');
        $this->addFilterToMap('landed_price', 'bbb.landed_price');
    }
}
