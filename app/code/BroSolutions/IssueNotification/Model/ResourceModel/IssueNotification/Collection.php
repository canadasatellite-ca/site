<?php

namespace BroSolutions\IssueNotification\Model\ResourceModel\IssueNotification;

/**
 * Class Collection
 * @package BroSolutions\IssueNotification\Model\ResourceModel\IssueNotification
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'BroSolutions\IssueNotification\Model\IssueNotification',
            'BroSolutions\IssueNotification\Model\ResourceModel\IssueNotification'
        );
    }
}
