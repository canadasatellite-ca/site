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

namespace Cart2Quote\Desk\Model\ResourceModel\Ticket\Priority;

/**
 * Ticket Priority collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Cart2Quote Collection Helper
     *
     * @var \Cart2Quote\Desk\Helper\ResourceModel\Collection
     */
    protected $_collectionHelper = null;

    /**
     * Ticket Priority Collection constructor
     *
     * @param \Cart2Quote\Desk\Helper\ResourceModel\Collection $collectionHelper
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Cart2Quote\Desk\Helper\ResourceModel\Collection $collectionHelper,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->_collectionHelper = $collectionHelper;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Collection model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cart2Quote\Desk\Model\Ticket\Priority', 'Cart2Quote\Desk\Model\ResourceModel\Ticket\Priority');
    }

    /**
     * Get the option array by priority id and code
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('priority_id', 'code');
    }

    /**
     * Sets array(label => data, value => data)
     * to array(-label- data, -value- data)
     *
     * @return array
     */
    public function toGridOptionArray()
    {
        return $this->_collectionHelper->toGridOptionArray($this);
    }
}
