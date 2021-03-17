<?php
namespace Magedelight\Faqs\Model\ResourceModel\Product;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
 
class Collection extends AbstractCollection
{   
    protected $_idFieldName = \Magedelight\Faqs\Model\Product::Question_ID;
    
    public function _construct()
    {
        $this->_init('Magedelight\Faqs\Model\Product', 'Magedelight\Faqs\Model\ResourceModel\Product');
    }
}
