<?php

namespace Magedelight\Faqs\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\AbstractModel;

class Product extends AbstractDb
{
    public function _construct()
    {
        $this->_init('md_faq_product', 'question_id');
    }
}
