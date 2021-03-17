<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\ResourceModel\Amazon\Report\Pricing;

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

        $this->getSelect()->joinInner(
            ['account' => $this->getTable('channel_amazon_account')],
            'main_table.merchant_id = account.merchant_id',
            ['account_name' => 'account.name']
        );

        $this->getSelect()->joinInner(
            ['bbb' => $this->getTable('channel_amazon_pricing_bestbuybox')],
            'main_table.asin = bbb.asin AND bbb.country_code = account.country_code',
            ['bbb_landed_price' => 'landed_price', 'bbb_condition' => 'condition_code', 'is_seller' => 'is_seller']
        )->joinInner(
            ['lowest' => $this->getTable('channel_amazon_pricing_lowest')],
            'main_table.asin = lowest.asin AND lowest.country_code = account.country_code',
            ['lowest_landed_price' => 'landed_price', 'lowest_condition' => 'condition_code']
        )->group('main_table.id');

        $this->addFilterToMap('id', 'main_table.id');
        $this->addFilterToMap('name', 'main_table.name');
        $this->addFilterToMap('merchant_id', 'main_table.merchant_id');
        $this->addFilterToMap('asin', 'main_table.asin');
        $this->addFilterToMap('bbb_condition', 'bbb.condition_code');
        $this->addFilterToMap('bbb_landed_price', 'bbb.landed_price');
        $this->addFilterToMap('lowest_condition', 'lowest.condition_code');
        $this->addFilterToMap('lowest_landed_price', 'lowest.landed_price');
    }
}
