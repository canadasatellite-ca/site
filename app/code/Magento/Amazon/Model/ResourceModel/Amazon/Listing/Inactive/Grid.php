<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\ResourceModel\Amazon\Listing\Inactive;

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
            Definitions::ERROR_LIST_STATUS
        ];

        $logTable = $this->getTable('channel_amazon_listing_log');

        $subquery = new \Zend_Db_Expr(
            '(
                SELECT id FROM ' . $logTable . ' AS error_log_sub
                WHERE error_log_sub.seller_sku = main_table.seller_sku AND error_log_sub.`action` = "Publish Listing"
                ORDER BY error_log_sub.id DESC
                LIMIT 1
            )'
        );

        $this->getSelect()->joinLeft(
            ['error_log' => $this->getTable('channel_amazon_listing_log')],
            'error_log.id = ' . $subquery,
            ['notes' => 'IFNULL(`error_log`.`notes`, "Not Provided")']
        )->group('main_table.id');

        // filter on inactive products only
        $this->addFieldToFilter('list_status', ['in' => $statuses]);

        $this->getSelect()->joinLeft(
            ['account' => $this->getTable('channel_amazon_account')],
            'main_table.merchant_id = account.merchant_id',
            ['account_name' => 'account.name']
        );

        $this->addFilterToMap('id', 'main_table.id');
        $this->addFilterToMap('notes', 'error_log.notes');
        $this->addFilterToMap('merchant_id', 'main_table.merchant_id');
        $this->addFilterToMap('condition', 'main_table.condition');
        $this->addFilterToMap('name', 'main_table.name');
    }
}
