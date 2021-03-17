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

/**
 * Used in creating options for Yes|No|Specified config value selection
 *
 */
namespace Cart2Quote\Desk\Model\Config\Source;

use Cart2Quote\Desk\Model\ResourceModel\Ticket\Priority\Collection;
use Magento\Framework\Option\ArrayInterface;
use \Cart2Quote\Desk\Helper\ResourceModel\Collection as Helper;

/**
 * Class Priority
 */
class Priority implements ArrayInterface
{
    /**
     * Priority Collection
     *
     * @var Collection
     */
    protected $_collection;

    /**
     * Cart2Quote Data Helper
     *
     * @var Helper
     */
    protected $_helper;

    /**
     * Class Priority constructor
     *
     * @param Collection $collection
     * @param Helper $helper
     */
    public function __construct(
        Collection $collection,
        Helper $helper
    ) {
        $this->_collection = $collection;
        $this->_helper = $helper;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_helper->ucfirstToOptionArray($this->_collection);
    }
}


