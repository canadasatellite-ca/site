<?php

namespace BroSolutions\IssueNotification\Model\ResourceModel;

/**
 * Class IssueNotification
 * @package BroSolutions\IssueNotification\Model\ResourceModel
 */
class IssueNotification extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('brosolutions_issue_notification', 'issue_id');
    }
}

