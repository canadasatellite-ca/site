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

namespace Cart2Quote\Desk\Helper;

use Magento\Store\Model\Store;

/**
 * Default Cart2Quote Desk helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_HELPDESK_ENABLED = 'desk_general/desk/enabled';
    const XML_PATH_DEFAULT_PRIORITY = 'desk_general/default_settings/priority';
    const XML_PATH_PRODUCT_PAGE_VISIBILITY = 'desk_general/default_settings/product_page_visibility';

    /**
     * Store Manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Filter manager
     *
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $_filter;

    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * Store
     *
     * @var \Magento\Store\Model\Store
     */
    protected $_store;

    /**
     * Class Data constructor
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\Filter\FilterManager $filter
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Filter\FilterManager $filter,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->_storeManager = $storeManager;
        $this->_escaper = $escaper;
        $this->_filter = $filter;
        parent::__construct($context);
    }

    /**
     * Return short detail info in HTML
     *
     * @param string $origDetail Full detail info
     * @return string
     */
    public function getDetailHtml($origDetail)
    {
        return nl2br($this->_filter->truncate($this->escapeHtml($origDetail), ['length' => 50]));
    }

    /**
     * Return label formatted for HTML
     *
     * @param string $label The label
     * @return string
     */
    public function getLabelHtml($label)
    {
        return $this->escapeHtml(ucfirst($label));
    }

    /**
     * Escape html for a string
     *
     * @param string $string
     * @return array|string
     */
    public function escapeHtml($string)
    {
        return $this->_escaper->escapeHtml($string);
    }

    /**
     * Returns true if desk is enabled
     *
     * @return boolean
     */
    public function getDeskEnabled()
    {
        return (bool)$this->getConfigValue(self::XML_PATH_HELPDESK_ENABLED, $this->getStore()->getStoreId());
    }

    /**
     * Get the default priority from config
     *
     * @return int
     */
    public function getDefaultPriority()
    {
        return $this->getConfigValue(self::XML_PATH_DEFAULT_PRIORITY, $this->getStore()->getStoreId());
    }

    /**
     * Returns true if Product Page Visibility is enabled
     *
     * @return boolean
     */
    public function getProductPageVisibility()
    {
        return $this->getConfigValue(self::XML_PATH_PRODUCT_PAGE_VISIBILITY, $this->getStore()->getStoreId());
    }

    /**
     * Set current store
     *
     * @param Store $store
     * @return void
     */
    public function setStore(Store $store)
    {
        $this->_store = $store;
    }

    /**
     * Return store
     *
     * @return Store
     */
    public function getStore()
    {
        if ($this->_store instanceof Store) {
            return $this->_store;
        }
        return $this->_storeManager->getStore();
    }

    /**
     * Return store configuration value
     *
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    protected function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
