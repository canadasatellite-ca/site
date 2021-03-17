<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model\ResourceModel\Amazon;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ListingMetrics extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            'channel_amazon_listing',
            'id'
        );
    }

    public function countListingsByStatusesPerMerchant(array $statuses, array $merchantIds): array
    {
        $select = $this->getConnection()->select();
        if (!$statuses || !$merchantIds) {
            return [];
        }
        $select->from($this->getMainTable(), [])
            ->where('list_status IN (?)', $statuses)
            ->where('merchant_id IN (?)', $merchantIds)
            ->columns([
                'merchant_id' => 'merchant_id',
                'count' => new \Zend_Db_Expr('count(id)')
            ])
            ->group('merchant_id');
        return $this->getConnection()->fetchPairs($select);
    }
}
