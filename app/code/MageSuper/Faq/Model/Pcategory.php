<?php

namespace MageSuper\Faq\Model;

class Pcategory extends \Magento\Framework\Model\AbstractModel
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
    public $eventObject = 'pcategory'; // parent value is 'object'

    /**
     * Name of object id field
     *
     * @var string
     */
    public $idFieldName = self::CATEGORY_ID;

    const STATUS_ENABLED = 1;
    
    const STATUS_DISABLED = 2;

    public $stdLib;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\DateTime $stdLib,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->stdLib = $stdLib;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('MageSuper\Faq\Model\ResourceModel\Pcategory');
    }


    public function beforeSave()
    {
        parent::beforeSave();

    }
}
