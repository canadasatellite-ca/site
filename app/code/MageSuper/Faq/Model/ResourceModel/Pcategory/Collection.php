<?php
namespace MageSuper\Faq\Model\ResourceModel\Pcategory;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
 
class Collection extends AbstractCollection
{   
    protected $_idFieldName = 'category_id';
    
    public function _construct()
    {
        $this->_init('MageSuper\Faq\Model\Pcategory', 'MageSuper\Faq\Model\ResourceModel\Pcategory');
    }
}
