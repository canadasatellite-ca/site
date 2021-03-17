<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Amazon\Order\View\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

class Order extends AbstractRenderer
{
    public function render(DataObject $row)
    {
        $magentoOrderNumber = $row->getData('sales_order_number');
        $magentoOrderId = $row->getData('sales_order_id');
        $orderId = $row->getData('order_id');

        if ($magentoOrderId) {
            $url = $this->getUrl('sales/order/view', ['order_id' => $magentoOrderId]);
            return '<a href="' . $url . '"  target="_blank">' . $magentoOrderNumber . '</a>';
        }

        return $orderId;
    }
}
