<?php
namespace MageSuper\Casat\Model\ResourceModel\OrderComment;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('MageSuper\Casat\Model\OrderComment','MageSuper\Casat\Model\ResourceModel\OrderComment');
    }
}
