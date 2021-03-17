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
 * @package     DeskEmail
 * @copyright   Copyright (c) 2016 Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */

namespace Cart2Quote\DeskEmail\Model\Container;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Email container that is used for sending an email
 *
 * Class Container
 */
abstract class Container implements IdentityInterface
{
    /**
     * Store Manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Desk store config
     *
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Store
     *
     * @var Store
     */
    protected $_store;

    /**
     * Main name for emailing
     *
     * @var string
     */
    protected $mainName;

    /**
     * Main email for emailing
     *
     * @var string
     */
    protected $mainEmail;

    /**
     * Class Container constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
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
        return $this->_scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Set current store
     *
     * @param Store $_store
     * @return void
     */
    public function setStore(Store $_store)
    {
        $this->_store = $_store;
    }

    /**
     * Return store
     *
     * @return Store
     */
    public function getStore()
    {
        //current store
        if ($this->_store instanceof Store) {
            return $this->_store;
        }
        return $this->_storeManager->getStore();
    }

    /**
     * Set main name
     *
     * @param string $name
     * @return void
     */
    public function setMainName($name)
    {
        $this->mainName = $name;
    }

    /**
     * Set main email
     *
     * @param string $email
     * @return void
     */
    public function setMainEmail($email)
    {
        $this->mainEmail = $email;
    }

    /**
     * Return main name
     *
     * @return string
     */
    public function getMainName()
    {
        return $this->mainName;
    }

    /**
     * Return main email
     *
     * @return string
     */
    public function getMainEmail()
    {
        return $this->mainEmail;
    }
}
