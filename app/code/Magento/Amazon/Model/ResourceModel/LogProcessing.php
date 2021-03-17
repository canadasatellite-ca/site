<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model\ResourceModel;

/**
 * Class LogProcessing
 */
class LogProcessing extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'channel_amazon_log_processing',
            'log_id'
        );
    }

    /**
     * @param array $logIds
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addIds(array $logIds)
    {
        if (empty($logIds)) {
            return;
        }
        $adapter = $this->_resources->getConnection();
        $adapter->insertArray($this->getMainTable(), ['log_id'], $logIds);
    }

    public function deleteByIds(array $logIds)
    {
        if (empty($logIds)) {
            return;
        }
        $adapter = $this->_resources->getConnection();
        $adapter->delete($this->getMainTable(), $adapter->quoteInto('log_id IN (?)', $logIds));
    }

    /**
     * @param \DateTimeInterface $dateTime
     * @throws \Magento\Framework\Exception\LocalizedException
     * @deprecated this method would be removed in the major release
     * @see \Magento\Amazon\Model\ResourceModel\LogProcessing::deleteLogsOlderThan
     */
    public function deleteBeforeDate(\DateTimeInterface $dateTime)
    {
        $adapter = $this->_resources->getConnection();
        $adapter->delete(
            $this->getMainTable(),
            $adapter->quoteInto('created_at < ?', $dateTime->format('Y-m-d H:i:s'))
        );
    }

    /**
     * @param int $minutes
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteLogsOlderThan(int $minutes): int
    {
        $adapter = $this->_resources->getConnection();
        return $adapter->delete(
            $this->getMainTable(),
            $adapter->quoteInto('created_at <  now() - interval ? MINUTE', $minutes)
        );
    }

    /**
     * @param int $minutes
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function countLogsOlderThan(int $minutes): int
    {
        $adapter = $this->_resources->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), ['count(*)'])
            ->where(
                $adapter->quoteInto('created_at < now() - interval ? MINUTE', $minutes)
            );
        return (int) $adapter->fetchOne($select);
    }

    /**
     * Looks for log ids and returns those which are present in the DB
     *
     * @param int[] $logIds
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function findByIds(array $logIds): array
    {
        if (empty($logIds)) {
            return [];
        }
        $adapter = $this->_resources->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), [])
            ->columns(['log_id'])
            ->where('log_id IN (?)', $logIds);
        return $adapter->fetchCol($select);
    }
}
