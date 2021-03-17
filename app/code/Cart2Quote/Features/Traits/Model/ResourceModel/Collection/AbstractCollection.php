<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\ResourceModel\Collection;
/**
 * Flat sales abstract collection
 */
trait AbstractCollection
{
    /**
     * Get select count sql
     *
     * @return \Zend_Db_Select
     */
    private function getSelectCountSql()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!$this->_countSelect instanceof \Zend_Db_Select) {
            $this->setSelectCountSql(parent::getSelectCountSql());
        }
        return $this->_countSelect;
		}
	}
    /**
     * Set select count sql
     *
     * @param \Zend_Db_Select $countSelect
     * @return $this
     */
    private function setSelectCountSql(\Zend_Db_Select $countSelect)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_countSelect = $countSelect;
        return $this;
		}
	}
    /**
     * Add attribute to select result set. Backward compatibility with EAV collection
     *
     * @param string $attribute
     * @return $this
     */
    private function addAttributeToSelect($attribute)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->addFieldToSelect($this->_attributeToField($attribute));
        return $this;
		}
	}
    /**
     * Check if $attribute is \Magento\Eav\Model\Entity\Attribute and convert to string field name
     *
     * @param string|\Magento\Eav\Model\Entity\Attribute $attribute
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function _attributeToField($attribute)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$field = false;
        if (is_string($attribute)) {
            $field = $attribute;
        } elseif ($attribute instanceof \Magento\Eav\Model\Entity\Attribute) {
            $field = $attribute->getAttributeCode();
        }
        if (!$field) {
            throw new \Magento\Framework\Exception\LocalizedException(__('We cannot determine the field name.'));
        }
        return $field;
		}
	}
    /**
     * Specify collection select filter by attribute value. Backward compatibility with EAV collection
     *
     * @param string|\Magento\Eav\Model\Entity\Attribute $attribute
     * @param array|int|string|null $condition
     * @return $this
     */
    private function addAttributeToFilter($attribute, $condition = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->addFieldToFilter($this->_attributeToField($attribute), $condition);
        return $this;
		}
	}
    /**
     * Specify collection select order by attribute value. Backward compatibility with EAV collection
     *
     * @param string $attribute
     * @param string $dir
     * @return $this
     */
    private function addAttributeToSort($attribute, $dir = 'asc')
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->addOrder($this->_attributeToField($attribute), $dir);
        return $this;
		}
	}
    /**
     * Set collection page start and records to show. Backward compatibility with EAV collection
     *
     * @param int $pageNum
     * @param int $pageSize
     * @return $this
     */
    private function setPage($pageNum, $pageSize)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setCurPage($pageNum)->setPageSize($pageSize);
        return $this;
		}
	}
    /**
     * Retrieve all ids for collection. Backward compatibility with EAV collection
     *
     * @param null|int $limit
     * @param null|int $offset
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getAllIds($limit = null, $offset = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
		}
	}
    /**
     * Create all ids retrieving select with limitation. Backward compatibility with EAV collection
     *
     * @param null|int $limit
     * @param null|int $offset
     * @return \Magento\Framework\DB\Select
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function _getAllIdsSelect($limit = null, $offset = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$idsSelect = clone $this->getSelect();
        $idsSelect->reset(\Zend_Db_Select::ORDER);
        $idsSelect->reset(\Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(\Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(\Zend_Db_Select::COLUMNS);
        $idsSelect->columns($this->getResource()->getIdFieldName(), 'main_table');
        $idsSelect->limit($limit, $offset);
        return $idsSelect;
		}
	}
    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    private function getSearchCriteria()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return null;
		}
	}
    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     */
    private function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this;
		}
	}
    /**
     * Get total count.
     *
     * @return int
     */
    private function getTotalCount()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getSize();
		}
	}
    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     */
    private function setTotalCount($totalCount)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this;
		}
	}
    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return $this
     */
    private function setItems(array $items = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this;
		}
	}
    /**
     * Returns a collection item that corresponds to the fetched row
     * and moves the internal data pointer ahead
     * All returned rows marked as non changed to prevent unnecessary persistence operations
     *
     * @return  \Magento\Framework\Object|bool
     */
    private function fetchItem()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (null === $this->_fetchStmt) {
            $this->_renderOrders()->_renderLimit();
            $this->_fetchStmt = $this->getConnection()->query($this->getSelect());
        }
        $data = $this->_fetchStmt->fetch();
        if (!empty($data) && is_array($data)) {
            /** @var \Magento\Sales\Model\AbstractModel $item */
            $item = $this->getNewEmptyItem();
            if ($this->getIdFieldName()) {
                $item->setIdFieldName($this->getIdFieldName());
            }
            $item->setData($data);
            $this->entitySnapshot->registerSnapshot($item);
            return $item;
        }
        return false;
		}
	}
    /**
     * Load data with filter in place.
     * - All returned rows marked as non changed to prevent unnecessary persistence operations
     *
     * @param   bool $printQuery
     * @param   bool $logQuery
     * @return  $this
     */
    private function loadWithFilter($printQuery = false, $logQuery = false)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_beforeLoad();
        $this->_renderFilters()->_renderOrders()->_renderLimit();
        $this->printLogQuery($printQuery, $logQuery);
        $data = $this->getData();
        $this->resetData();
        if (is_array($data)) {
            foreach ($data as $row) {
                $item = $this->getNewEmptyItem();
                if ($this->getIdFieldName()) {
                    $item->setIdFieldName($this->getIdFieldName());
                }
                $item->addData($row);
                $this->entitySnapshot->registerSnapshot($item);
                $this->addItem($item);
            }
        }
        $this->_setIsLoaded();
        $this->_afterLoad();
        return $this;
		}
	}
}
