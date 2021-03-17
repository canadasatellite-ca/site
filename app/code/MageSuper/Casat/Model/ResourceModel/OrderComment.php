<?php
namespace MageSuper\Casat\Model\ResourceModel;
class OrderComment extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magesuper_casat_ordercomment','magesuper_casat_ordercomment_id');
    }
}
