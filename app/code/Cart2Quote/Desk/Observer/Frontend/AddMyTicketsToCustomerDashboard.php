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

namespace Cart2Quote\Desk\Observer\Frontend;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class AddMyTicketsToCustomerDashboard
 */
class AddMyTicketsToCustomerDashboard implements ObserverInterface
{
    /**
     * Data helper
     *
     * @var \Cart2Quote\Desk\Helper\Data
     */
    protected $_helper;

    /**
     * Class AddMyTicketsToCustomerDashboard constructor
     *
     * @param \Cart2Quote\Desk\Helper\Data $helper
     */
    public function __construct(
        \Cart2Quote\Desk\Helper\Data $helper
    ) {
        $this->_helper = $helper;
    }

    /**
     * Adds the My Tickets to the customer Dashboard
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->isCustomerAccountNavigation($observer) && $this->_helper->getDeskEnabled()) {
            $current = false;
            $moduleName = $observer->getBlock()->getRequest()->getModuleName();
            if ($moduleName == 'desk') {
                $current = true;
            }
            $observer->getBlock()->addChild(
                'customer-account-navigation-desk-tickets-link',
                'Magento\Framework\View\Element\Html\Link\Current',
                [
                    'path' => 'desk/customer',
                    'label' => 'My Tickets',
                    'current' => $current
                ]
            );
        }

        return $this;
    }

    /**
     * Check if this is the customer account navigation block
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return bool
     */
    protected function isCustomerAccountNavigation(\Magento\Framework\Event\Observer $observer)
    {
        return $observer->getBlock()->getNameInLayout() == 'customer_account_navigation';
    }
}
