<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\ResourceModel\Amazon;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Attribute
 */
class Attribute extends AbstractDb
{
    const CHUNK_SIZE = 1000;

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            'channel_amazon_attribute',
            'id'
        );
    }

    /**
     * Inserts Amazon attributes
     *
     * @param array $data
     * @return void
     * @throws LocalizedException
     */
    public function insert(array $data)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $tableName = $this->getMainTable();
        $connection->insertOnDuplicate($tableName, $data, []);
    }
}
