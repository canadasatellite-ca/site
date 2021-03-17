<?php
namespace Magedelight\Faqs\Model\ResourceModel\Category;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    // @codingStandardsIgnoreStart
    protected $_idFieldName = \Magedelight\Faqs\Model\Category::CATEGORY_ID;
     
    protected function _construct()
    {
        $this->_init('Magedelight\Faqs\Model\Category', 'Magedelight\Faqs\Model\ResourceModel\Category');
    }
    // @codingStandardsIgnoreEnd
}
