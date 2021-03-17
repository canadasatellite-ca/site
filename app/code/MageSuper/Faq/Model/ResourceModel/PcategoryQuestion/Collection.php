<?php
namespace MageSuper\Faq\Model\ResourceModel\PcategoryQuestion;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
 
class Collection extends AbstractCollection
{   
    protected $_idFieldName = 'pcategory_id';
    
    public function _construct()
    {
        $this->_init('MageSuper\Faq\Model\PcategoryQuestion', 'MageSuper\Faq\Model\ResourceModel\PcategoryQuestion');
    }
}
