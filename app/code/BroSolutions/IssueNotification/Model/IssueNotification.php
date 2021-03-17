<?php

namespace BroSolutions\IssueNotification\Model;

/**
 * Class IssueNotification
 * @package BroSolutions\IssueNotification\Model
 */
class IssueNotification extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('BroSolutions\IssueNotification\Model\ResourceModel\IssueNotification');
    }
}
