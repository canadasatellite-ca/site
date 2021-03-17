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

namespace Cart2Quote\Desk\Controller\Customer;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class View
 */
class View extends \Cart2Quote\Desk\Controller\Customer\Customer
{
    /**
     * Render ticket details
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        if ($navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('desk/customer');
        }
        $this->setTitle($resultPage);

        return $resultPage;
    }

    /**
     * Sets the title on the view page.
     *
     * @param \Magento\Framework\View\Result\Page $resultPage
     *
     * @return void
     */
    protected function setTitle(\Magento\Framework\View\Result\Page $resultPage)
    {
        $ticketId = $this->getRequest()->getParam('id');
        if ($ticketId) {
            $title = __("Ticket #%1", $ticketId);
        } else {
            $title = __("Ticket Details");
        }
        $resultPage->getConfig()->getTitle()->set($title);
    }
}
