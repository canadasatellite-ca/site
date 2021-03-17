<?php
/**
 *
 * CART2QUOTE CONFIDENTIAL
 * __________________
 *
 *  [2009] - [2016] Cart2Quote B.V.
 *  All Rights Reserved.
 *
 * NOTICE OF LICENSE
 *
 * All information contained herein is, and remains
 * the property of Cart2Quote B.V. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to Cart2Quote B.V.
 * and its suppliers and may be covered by European and Foreign Patents,
 * patents in process, and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Cart2Quote B.V.
 *
 * @category    Cart2Quote
 * @package     Desk
 * @copyright   Copyright (c) 2016 Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */

namespace Cart2Quote\Desk\Helper\Action;

use Magento\Framework\Exception\LocalizedException;

/**
 * Action pager helper for iterating over search results
 */
class Pager extends \Magento\Framework\App\Helper\AbstractHelper
{
    const STORAGE_PREFIX = 'search_result_ids';

    /**
     * Storage id
     *
     * @var int
     */
    protected $_storageId = null;

    /**
     * Array of items
     *
     * @var array
     */
    protected $_items = null;

    /**
     * Backend session model
     *
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * Class Page constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Backend\Model\Session $backendSession
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\Session $backendSession
    ) {
        $this->_backendSession = $backendSession;
        parent::__construct($context);
    }

    /**
     * Set storage id
     *
     * @param int $storageId
     * @return void
     */
    public function setStorageId($storageId)
    {
        $this->_storageId = $storageId;
    }

    /**
     * Set items to storage
     *
     * @param array $items
     * @return $this
     */
    public function setItems(array $items)
    {
        $this->_items = $items;
        $this->_backendSession->setData($this->_getStorageKey(), $this->_items);

        return $this;
    }

    /**
     * Load stored items
     *
     * @return void
     */
    protected function _loadItems()
    {
        if ($this->_items == null) {
            $this->_items = (array)$this->_backendSession->getData($this->_getStorageKey());
        }
    }

    /**
     * Get next item id
     *
     * @param int $id
     * @return int|bool
     */
    public function getNextItemId($id)
    {
        $position = $this->_findItemPositionByValue($id);
        if ($position === false || $position == count($this->_items) - 1) {
            return false;
        }

        return $this->_items[$position + 1];
    }

    /**
     * Get previous item id
     *
     * @param int $id
     * @return int|bool
     */
    public function getPreviousItemId($id)
    {
        $position = $this->_findItemPositionByValue($id);
        if ($position === false || $position == 0) {
            return false;
        }

        return $this->_items[$position - 1];
    }

    /**
     * Return item position based on passed in value
     *
     * @param string $value
     * @return int|bool
     */
    protected function _findItemPositionByValue($value)
    {
        $this->_loadItems();
        return array_search($value, $this->_items);
    }

    /**
     * Get storage key
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getStorageKey()
    {
        if (!$this->_storageId) {
            throw new LocalizedException(__("Storage key was not set"));
        }

        return self::STORAGE_PREFIX . $this->_storageId;
    }
}
