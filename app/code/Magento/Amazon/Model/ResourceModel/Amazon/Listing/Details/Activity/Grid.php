<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\ResourceModel\Amazon\Listing\Details\Activity;

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
        $this->getSelect()->joinLeft(
            ['listing' => $this->getTable('channel_amazon_listing')],
            'main_table.seller_sku = listing.seller_sku AND main_table.merchant_id = listing.merchant_id',
            ['merchant_id', 'main_table.seller_sku', 'asin']
        );

        parent::_initSelect();

        $this->addFilterToMap('id', 'listing.id');
    }
}
