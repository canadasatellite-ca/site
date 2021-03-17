<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Plugin\Model;

use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Api\QueueManagementInterface;
use Magento\Sales\Model\Order as SalesOrder;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;
use Aheadworks\AdvancedReviews\Model\Source\Email\Type as NotificationType;

/**
 * Class Order
 * @package Aheadworks\AdvancedReviews\Plugin\Model
 */
class Order
{
    /**
     * @var QueueManagementInterface
     */
    private $queueManagement;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var string[]|null
     */
    private $orderState;

    /**
     * @param QueueManagementInterface $queueManagement
     * @param Config $config
     */
    public function __construct(
        QueueManagementInterface $queueManagement,
        Config $config
    ) {
        $this->queueManagement = $queueManagement;
        $this->config = $config;
    }

    /**
     * Store order status
     *
     * @param OrderResource $subject
     * @param \Closure $proceed
     * @param SalesOrder $order
     * @param string $value
     * @param null $field
     * @return OrderResource
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundLoad(
        OrderResource $subject,
        \Closure $proceed,
        SalesOrder $order,
        $value,
        $field = null
    ) {
        $result = $proceed($order, $value, $field);

        if ($order->getId()) {
            $this->orderState[$order->getId()] = $order->getState();
        }

        return $result;
    }

    /**
     * Add item to queue
     *
     * @param OrderResource $subject,
     * @param \Closure $proceed
     * @param SalesOrder $order
     * @return OrderResource
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        OrderResource $subject,
        \Closure $proceed,
        SalesOrder $order
    ) {
        $result = $proceed($order);

        if ($this->canAddReminder($order)) {
            $this->queueManagement->add(
                NotificationType::SUBSCRIBER_REVIEW_REMINDER,
                $order->getId(),
                $order->getStoreId(),
                $this->getCustomerName($order),
                $order->getCustomerEmail()
            );
        }
        return $result;
    }

    /**
     * Check can add reminder notification
     *
     * @param SalesOrder $order
     * @return bool
     */
    private function canAddReminder(SalesOrder $order)
    {
        return $this->config->isReviewReminderEnabled()
            && ($order->getId()
                && isset($this->orderState[$order->getId()])
                && $this->orderState[$order->getId()] != $order->getState()
                && $order->getState() == SalesOrder::STATE_COMPLETE);
    }

    /**
     * Retrieve customer name
     *
     * @param SalesOrder $order
     * @return string
     */
    private function getCustomerName(SalesOrder $order)
    {
        $customerName = $order->getBillingAddress()->getFirstname();

        return !empty($customerName) ? $customerName : '';
    }
}
