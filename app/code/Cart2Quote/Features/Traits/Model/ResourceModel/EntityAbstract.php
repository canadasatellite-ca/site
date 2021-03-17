<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\ResourceModel;
/**
 * Abstract quotation entity provides to its children knowledge about eventPrefix and eventObject
 */
trait EntityAbstract
{
    /**
     * Save entity
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    private function save(\Magento\Framework\Model\AbstractModel $object)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($object->isDeleted()) {
            return $this->delete($object);
        }
        if (!$this->entitySnapshot->isModified($object)) {
            $this->entityRelationComposite->processRelations($object);
            return $this;
        }
        $this->beginTransaction();
        try {
            $object->validateBeforeSave();
            $object->beforeSave();
            if ($object->isSaveAllowed()) {
                $this->_serializeFields($object);
                $this->_beforeSave($object);
                $this->_checkUnique($object);
                $this->objectRelationProcessor->validateDataIntegrity($this->getMainTable(), $object->getData());
                if ($object->getId() !== null && (!$this->_useIsObjectNew || !$object->isObjectNew())) {
                    $condition = $this->getConnection()->quoteInto($this->getIdFieldName() . '=?', $object->getId());
                    $data = $this->_prepareDataForSave($object);
                    unset($data[$this->getIdFieldName()]);
                    $this->getConnection()->update($this->getMainTable(), $data, $condition);
                } else {
                    $bind = $this->_prepareDataForSave($object);
                    unset($bind[$this->getIdFieldName()]);
                    $this->getConnection()->insert($this->getMainTable(), $bind);
                    $object->setId($this->getConnection()->lastInsertId($this->getMainTable()));
                    if ($this->_useIsObjectNew) {
                        $object->isObjectNew(false);
                    }
                }
                $this->unserializeFields($object);
                $this->_afterSave($object);
                $this->entitySnapshot->registerSnapshot($object);
                $object->afterSave();
                $this->entityRelationComposite->processRelations($object);
            }
            $this->addCommitCallback([$object, 'afterCommitCallback'])->commit();
            $object->setHasDataChanges(false);
        } catch (\Exception $e) {
            $this->rollBack();
            $object->setHasDataChanges(true);
            throw $e;
        }
        return $this;
		}
	}
    /**
     * Perform actions after object delete
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    private function _afterDelete(\Magento\Framework\Model\AbstractModel $object)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			parent::_afterDelete($object);
        return $this;
		}
	}
    /**
     * Perform actions after object load, mark loaded data as data without changes
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\Object $object
     * @return $this
     */
    private function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->entitySnapshot->registerSnapshot($object);
        return $this;
		}
	}
}
