<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\ResourceModel\Amazon;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Defect
 */
class Defect extends AbstractDb
{
    const CHUNK_SIZE = '1000';

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            'channel_amazon_defect',
            'id'
        );
    }

    /**
     * Inserts listing defects
     *
     * @param array $data
     * @param int $merchantId
     * @return void
     */
    public function insert(array $data, $merchantId)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $tableName = $this->getMainTable();

        $connection->insertOnDuplicate($tableName, $data, []);
    }

    /**
     * Deleted listing defects by seller_sku / alert_type
     *
     * @param string $sellerSku
     * @param string $alertType
     * @return void
     */
    public function remove($sellerSku, $alertType, $merchantId)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $tableName = $this->getMainTable();

        $where = [
            'merchant_id = ?' => $merchantId,
            'seller_sku = ?' => $sellerSku,
            'alert_type = ?' => $alertType
        ];

        $connection->delete($tableName, $where);
    }
}
