<?php
namespace Magedelight\Faqs\Model\ResourceModel\Question;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
 
class Collection extends AbstractCollection
{   
    protected $_idFieldName = \Magedelight\Faqs\Model\Question::Question_ID;
    
    public function _construct()
    {
        $this->_init('Magedelight\Faqs\Model\Question', 'Magedelight\Faqs\Model\ResourceModel\Question');
    }
}
