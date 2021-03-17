<?php
namespace MageSuper\Faq\Model\ResourceModel\QcategoryProduct;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
 
class Collection extends AbstractCollection
{   
    protected $_idFieldName = 'product_id';
    
    public function _construct()
    {
        $this->_init('MageSuper\Faq\Model\QcategoryProduct', 'MageSuper\Faq\Model\ResourceModel\QcategoryProduct');
    }
}
