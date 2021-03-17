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
 * Class AddAskQuestionsTab
 */
class AddAskQuestionsTab implements ObserverInterface
{
    /**
     * Cart2Quote Data Helper
     *
     * @var \Cart2Quote\Desk\Helper\Data
     */
    protected $_helper;

    /**
     * Class AddAskQuestionsTabs constructor
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
        if ( $this->isProductInfoDetailsBlock($observer) && $this->_helper->getDeskEnabled()
            && $this->_helper->getProductPageVisibility()) {
            /** @var \Magento\Catalog\Block\Product\View\Description */
            $block = $observer->getBlock();

            $block->addChild(
                'product.ticket.tab',
                'Cart2Quote\Desk\Block\Product\Tab\Ticket'
            );

            $block->getLayout()->addToParentGroup(
                'product.info.details.product.ticket.tab',
                'detailed_info'
            );
        }

        return $this;
    }

    /**
     * Check if the block is the Product Info Details Block
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return bool
     */
    protected function isProductInfoDetailsBlock(\Magento\Framework\Event\Observer $observer)
    {
        return $observer->getBlock()->getNameInLayout() == 'product.info.details';
    }
}
