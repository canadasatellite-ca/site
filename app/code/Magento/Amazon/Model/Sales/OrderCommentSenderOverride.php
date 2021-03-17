<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Sales;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;

/**
 * Class OrderCommentSenderOverride
 */
class OrderCommentSenderOverride extends OrderCommentSender
{
    /**
     * @param Order $order
     * @return void
     */
    protected function prepareTemplate(Order $order)
    {
        parent::prepareTemplate($order);

        $paymentMethod = $order->getPayment()->getMethod();

        if ($paymentMethod == 'amazonpayment') {
            $templateId = 'amazon_order_update_guest';
        } else {
            $templateId = $order->getCustomerIsGuest() ?
                $this->identityContainer->getGuestTemplateId() :
                $this->identityContainer->getTemplateId();
        }

        $this->templateContainer->setTemplateId($templateId);
    }
}
