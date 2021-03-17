<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\ResourceModel\Amazon;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Action
 */
class Action extends AbstractDb
{
    /** @var int */
    const CHUNK_SIZE = 1000;

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            'channel_amazon_action',
            'id'
        );
    }

    /**
     * Inserts api actions
     *
     * @param array $data
     * @return void
     */
    public function insert(array $data)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $tableName = $this->getMainTable();

        try {
            $connection->beginTransaction();
            $connection->insertOnDuplicate($tableName, $data, []);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }

    /**
     * Delete actions by id
     *
     * @param array $ids
     * @return void
     */
    public function deleteByIds(array $ids)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $tableName = $this->getMainTable();

        foreach (array_chunk($ids, self::CHUNK_SIZE) as $chunkIds) {
            $where = [
                'id IN (?)' => $chunkIds
            ];

            $connection->delete($tableName, $where);
        }
    }

    /**
     * Get records by given merchant id.
     *
     * @param int $merchantId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByMerchantId(int $merchantId)
    {
        $connection = $this->getConnection();
        $tableName = $this->getMainTable();

        $select = $connection->select()
            ->from($tableName)
            ->where('merchant_id = ?', $merchantId)
            ->where('command != ?', '');

        return $connection->fetchAll($select);
    }
}
