<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\ResourceModel;
/**
 * Quote resource model
 *
 * @package Cart2Quote\Quotation\Model\ResourceModel
 */
trait Quote
{
    /**
     * Save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    private function save(\Magento\Framework\Model\AbstractModel $object)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			//quote_id refers to the quotation_quote whereas the entity_id is the magento quote
        if ($object instanceof \Cart2Quote\Quotation\Model\Quote) {
            if (!$object->getQuoteId()) {
                $quote = $this->quoteFactory->create();
                $quote->setData($object->getData());
                $this->quoteResourceModel->save($quote);
                $object->setQuoteId($quote->getId());
            }
            return parent::save($object);
        }
        $this->quoteResourceModel->save($object);
        return $this;
		}
	}
    /**
     * Initialize table and PK name
     *
     * @return void
     */
    private function _construct()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_init('quotation_quote', 'quote_id');
		}
	}
    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Zend_Db_Select
     */
    private function _getLoadSelect($field, $value, $object)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$mainTable = $this->getMainTable();
        $field = $this->getConnection()->quoteIdentifier(sprintf('%s.%s', $mainTable, $field));
        $select = $this->getConnection()->select()
            ->from($mainTable)
            ->joinLeft(
                ['q' => $this->quoteResourceModel->getTable('quote')],
                '`q`.entity_id = ' . $mainTable . '.quote_id'
            )
            ->where($field . '=?', $value);
        return $select;
		}
	}
    /**
     * Perform actions before object save
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\Object $object
     * @return $this
     */
    private function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($object instanceof \Cart2Quote\Quotation\Model\EntityInterface && $object->getIncrementId() == null) {
            $newIncrementId = $this->sequenceManager->getSequence(
                $object->getEntityType(),
                $object->getStoreId()
            )->getNextValue();
            $object->setIncrementId($newIncrementId);
        }
        parent::_beforeSave($object);
        return $this;
		}
	}
}
