<?php

namespace MageSuper\Faq\Model;

class QcategoryProduct extends \Magento\Framework\Model\AbstractModel
{

    const CATEGORY_ID = 'category_id'; // We define the id fieldname

    /**
     * Prefix of model events names
     *
     * @var string
     */

    public $eventPrefix = 'faqs'; // parent value is 'core_abstract'

    /**
     * Name of the event object
     *
     * @var string
     */
    public $eventObject = 'qcategory_product'; // parent value is 'object'

    /**
     * Name of object id field
     *
     * @var string
     */
    public $idFieldName = self::CATEGORY_ID;

    protected function _construct()
    {
        $this->_init('MageSuper\Faq\Model\ResourceModel\QcategoryProduct');
    }
}
