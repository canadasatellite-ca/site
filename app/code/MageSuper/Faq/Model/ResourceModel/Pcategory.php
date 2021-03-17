<?php

namespace MageSuper\Faq\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\AbstractModel;

class Pcategory extends AbstractDb
{
    public function _construct()
    {
        $this->_init('md_pcategory_category', 'category_id');
    }
}
