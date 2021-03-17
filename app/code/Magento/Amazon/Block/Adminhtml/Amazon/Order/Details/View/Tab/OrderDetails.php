<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Amazon\Order\Details\View\Tab;

use Magento\Amazon\Api\Data\OrderInterface;
use Magento\Amazon\Api\OrderRepositoryInterface;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class OrderDetails
 */
class OrderDetails extends Template
{
    /** @var OrderRepositoryInterface $orderRepository */
    protected $orderRepository;

    /**
     * @param Context $context
     * @param OrderRepositoryInterface $orderRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->orderRepository = $orderRepository;
    }

    /**
     * Returns $orderInterface object
     *
     * @return bool | OrderInterface
     */
    public function loadOrderInterface()
    {
        /** @var int */
        $orderId = $this->getRequest()->getParam('id');

        try {
            /** @var OrderInterface */
            return $this->orderRepository->getByOrderId($orderId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }
}
