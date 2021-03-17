<?php
namespace MageSuper\Faq\Model\ResourceModel\Faq;
 
use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
 
class Collection extends AbstractCollection
{
   // @codingStandardsIgnoreStart
    protected $_idFieldName = \Magedelight\Faqs\Model\Faq::FAQ_ID;
     
    /**
     * Define resource model
     *
     * @return void
     */
   
    protected function _construct()
    {
        $this->_init('MageSuper\Faq\Model\Faq', 'MageSuper\Faq\Model\ResourceModel\Faq');
    }
    // @codingStandardsIgnoreEnd
}
