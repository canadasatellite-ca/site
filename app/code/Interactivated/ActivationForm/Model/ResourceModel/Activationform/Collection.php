<?php
namespace Interactivated\ActivationForm\Model\ResourceModel\Activationform;
 
use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
 
class Collection extends AbstractCollection
{
   // @codingStandardsIgnoreStart
    protected $_idFieldName = \Interactivated\ActivationForm\Model\Activationform::REQUEST_ID;
     
    /**
     * Define resource model
     *
     * @return void
     */
   
    protected function _construct()
    {
        $this->_init('Interactivated\ActivationForm\Model\Activationform', 'Interactivated\ActivationForm\Model\ResourceModel\Activationform');
    }
    // @codingStandardsIgnoreEnd
}
