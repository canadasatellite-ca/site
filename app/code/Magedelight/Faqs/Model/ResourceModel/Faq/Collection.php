<?php
namespace Magedelight\Faqs\Model\ResourceModel\Faq;
 
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
        $this->_init('Magedelight\Faqs\Model\Faq', 'Magedelight\Faqs\Model\ResourceModel\Faq');
    }
    // @codingStandardsIgnoreEnd
}
