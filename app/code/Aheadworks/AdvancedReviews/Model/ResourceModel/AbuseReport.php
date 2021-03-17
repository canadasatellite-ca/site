<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\ResourceModel;

use Aheadworks\AdvancedReviews\Api\Data\AbuseReportInterface;
use Aheadworks\AdvancedReviews\Model\Source\AbuseReport\Status;

/**
 * Class AbuseReport
 * @package Aheadworks\AdvancedReviews\Model\ResourceModel
 */
class AbuseReport extends AbstractResource
{
    /**#@+
     * Constants defined for tables
     * used by corresponding entity
     */
    const MAIN_TABLE_NAME = 'aw_ar_abuse_report';
    const MAIN_TABLE_ID_FIELD_NAME = 'id';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, self::MAIN_TABLE_ID_FIELD_NAME);
    }

    /**
     * Retrieve report id by entity
     *
     * @param string $entityType
     * @param int $entityId
     * @return string
     */
    public function getReportIdByEntity($entityType, $entityId)
    {
        $select = $this->getConnection()->select()
            ->from([$this->getTable(self::MAIN_TABLE_NAME)], [AbuseReportInterface::ID])
            ->where(AbuseReportInterface::ENTITY_TYPE . ' = ?', $entityType)
            ->where(AbuseReportInterface::ENTITY_ID . ' = ?', $entityId);

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * Ignore reports for entities
     *
     * @param string $entityType
     * @param array $ids
     */
    public function ignoreForEntity($entityType, $ids)
    {
        $this->getConnection()->update(
            $this->getTable(self::MAIN_TABLE_NAME),
            [AbuseReportInterface::STATUS => Status::PROCESSED],
            [
                AbuseReportInterface::ENTITY_TYPE . ' = ?' => $entityType,
                AbuseReportInterface::ENTITY_ID . ' IN (?)' => $ids
            ]
        );
    }
}
