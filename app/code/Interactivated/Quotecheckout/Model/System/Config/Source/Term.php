<?php

namespace Interactivated\Quotecheckout\Model\System\Config\Source;

use Magento\CheckoutAgreements\Model\ResourceModel\Agreement\CollectionFactory;

class Term implements \Magento\Framework\Option\ArrayInterface
{
    const STATUS_OPTIONAL   = 1;
    const STATUS_REQUIRED	= 2;
    const STATUS_HIDE	    = 0;

    /**
     * @var \Magento\CheckoutAgreements\Model\ResourceModel\Agreement\CollectionFactory
     */
    protected $_agreementCollectionFactory;

    /**
     * @param CollectionFactory $agreementCollectionFactory
     */
    public function __construct(
        CollectionFactory $agreementCollectionFactory
    ) {
        $this->_agreementCollectionFactory = $agreementCollectionFactory;
    }

    public function toOptionArray()
    {
        $agreement = $this->getAllTermConditions();

        return $agreement;
    }

    public function getAllTermConditions()
    {
        $models = $this->_agreementCollectionFactory->create()->getData();
        $result = [];
        $result[0] = "Custom agreement";
        foreach ($models as $model) {
            $result[$model['agreement_id']] = $model['name'];
        }

        return $result;
    }

    public function getTermById($termId)
    {
        $models = $this->_agreementCollectionFactory->create()
            ->addFieldToFilter('agreement_id', $termId)
            ->getData();

        return $models[0]['content'];
    }
}
