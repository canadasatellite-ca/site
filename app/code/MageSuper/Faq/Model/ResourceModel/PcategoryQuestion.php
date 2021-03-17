<?php

namespace MageSuper\Faq\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\AbstractModel;

class PcategoryQuestion extends AbstractDb
{
    public function _construct()
    {
        $this->_init('md_pcategory_question', 'pcategory_id');
    }
}
