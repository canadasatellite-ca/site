<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Status;
use Magento\Sales\Model\AbstractModel;
use Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface;
/**
 * Quote status history comments
 *
 * @method \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\History _getResource()
 * @method \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\History getResource()
 */
trait History
{
    /**
     * Notification flag
     *
     * @param  mixed $flag OPTIONAL (notification is not applicable by default)
     * @return $this
     */
    private function setIsCustomerNotified($flag = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($flag === null) {
            $flag = self::CUSTOMER_NOTIFICATION_NOT_APPLICABLE;
        }
        return $this->setData('is_customer_notified', $flag);
		}
	}
    /**
     * Customer Notification Applicable check method
     *
     * @return boolean
     */
    private function isCustomerNotificationNotApplicable()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getIsCustomerNotified() == self::CUSTOMER_NOTIFICATION_NOT_APPLICABLE;
		}
	}
    /**
     * Returns is_customer_notified
     *
     * @return int
     */
    private function getIsCustomerNotified()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::IS_CUSTOMER_NOTIFIED);
		}
	}
    /**
     * Retrieve status label
     *
     * @return string|null
     */
    private function getStatusLabel()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->getQuote()) {
            return $this->getQuote()->getConfig()->getStatusLabel($this->getStatus());
        }
        return null;
		}
	}
    /**
     * Retrieve quote instance
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    private function getQuote()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->_quote;
		}
	}
    /**
     * Set quote object and grab some metadata from it
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return $this
     */
    private function setQuote(\Cart2Quote\Quotation\Model\Quote $quote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_quote = $quote;
        $this->setStoreId($quote->getStoreId());
        return $this;
		}
	}
    /**
     * Returns status
     *
     * @return string
     */
    private function getStatus()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::STATUS);
		}
	}
    /**
     * Get store object
     *
     * @return \Magento\Store\Model\Store
     */
    private function getStore()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->getQuote()) {
            return $this->getQuote()->getStore();
        }
        return $this->_storeManager->getStore();
		}
	}
    /**
     * Set quote again if required
     *
     * @return $this
     */
    private function beforeSave()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			parent::beforeSave();
        if (!$this->getParentId() && $this->getQuote()) {
            $this->setParentId($this->getQuote()->getId());
        }
        return $this;
		}
	}
    /**
     * Returns parent_id
     *
     * @return int
     */
    private function getParentId()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::PARENT_ID);
		}
	}
    /**
     * Set parent id
     *
     * @param int $id
     * @return \Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface|History
     */
    private function setParentId($id)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::PARENT_ID, $id);
		}
	}
    /**
     * Returns comment
     *
     * @return string
     */
    private function getComment()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::COMMENT);
		}
	}
    /**
     * Returns created_at
     *
     * @return string
     */
    private function getCreatedAt()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::CREATED_AT);
		}
	}
    /**
     * Set created at
     *
     * @param string $createdAt
     * @return \Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface|History
     */
    private function setCreatedAt($createdAt)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::CREATED_AT, $createdAt);
		}
	}
    /**
     * Returns entity_id
     *
     * @return int
     */
    private function getEntityId()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::ENTITY_ID);
		}
	}
    /**
     * Returns entity_name
     *
     * @return string
     */
    private function getEntityName()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::ENTITY_NAME);
		}
	}
    /**
     * Returns is_visible_on_front
     *
     * @return int
     */
    private function getIsVisibleOnFront()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::IS_VISIBLE_ON_FRONT);
		}
	}
    /**
     * Set is visible on frontend
     *
     * @param int $isVisibleOnFront
     * @return \Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface|History
     */
    private function setIsVisibleOnFront($isVisibleOnFront)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(
            \Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::IS_VISIBLE_ON_FRONT,
            $isVisibleOnFront
        );
		}
	}
    /**
     * Set comment
     *
     * @param string $comment
     * @return \Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface|History
     */
    private function setComment($comment)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::COMMENT, $comment);
		}
	}
    /**
     * Set Status
     *
     * @param string $status
     * @return \Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface|History
     */
    private function setStatus($status)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::STATUS, $status);
		}
	}
    /**
     * Set entity name
     *
     * @param string $entityName
     * @return \Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface|History
     */
    private function setEntityName($entityName)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteStatusHistoryInterface::ENTITY_NAME, $entityName);
		}
	}
    /**
     * Get extention attributes
     *
     * @return \Magento\Sales\Api\Data\OrderStatusHistoryExtensionInterface|null
     */
    private function getExtensionAttributes()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->_getExtensionAttributes();
		}
	}
    /**
     * Set extention attributes
     *
     * @param \Magento\Sales\Api\Data\OrderStatusHistoryExtensionInterface $extensionAttributes
     * @return $this
     */
    private function setExtensionAttributes(
        \Magento\Sales\Api\Data\OrderStatusHistoryExtensionInterface $extensionAttributes
    ) {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->_setExtensionAttributes($extensionAttributes);
		}
	}
    /**
     * Initialize resourcemodel
     *
     * @return void
     */
    private function _construct()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_init(\Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\History::class);
		}
	}
}
