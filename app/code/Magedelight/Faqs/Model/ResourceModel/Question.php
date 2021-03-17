<?php

namespace Magedelight\Faqs\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Question extends AbstractDb
{
    public function _construct()
    {
        $this->_init('md_category_question', 'question_id');
    }
}
