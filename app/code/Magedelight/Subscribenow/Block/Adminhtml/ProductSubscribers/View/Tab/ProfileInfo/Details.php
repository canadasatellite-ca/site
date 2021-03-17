<?php

/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Subscribenow
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Subscribenow\Block\Adminhtml\ProductSubscribers\View\Tab\ProfileInfo;

use Magedelight\Subscribenow\Block\Adminhtml\ProductSubscribers\View\Tab\ProfileInfo as ParentBlock;

class Details extends ParentBlock
{
    
    /**
     * Return subscription status
     * @param int $statusId
     * @return string|null
     */
    public function getSubscriptionStatus()
    {
        $statusId = $this->getSubscription()->getSubscriptionStatus();
        $status = $this->helper->getStatusLabel();
        return $status[$statusId];
    }
    
    /**
     * Get Order Qty
     * @return int
     */
    public function getQty()
    {
        $info = $this->getOrderItemInfo();
        $qty = isset($info['qty']) ? $info['qty'] : 0;
        return $qty;
    }

    /**
     * Product Admin URL
     * @return string
     */
    public function getProductUrl()
    {
        if ($this->getSubscriptionProduct() && $this->getSubscriptionProduct()->getId()) {
            return $this->getUrl('catalog/product/edit', ['id' => $this->getSubscriptionProduct()->getId()]);
        }
        return "#";
    }
}
