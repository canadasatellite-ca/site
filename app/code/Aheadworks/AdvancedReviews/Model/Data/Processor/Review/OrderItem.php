<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Data\Processor\Review;

use Aheadworks\AdvancedReviews\Model\Data\ProcessorInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Aheadworks\AdvancedReviews\Model\Source\Review\IsVerifiedBuyer as ReviewIsVerifiedBuyerSource;
use Aheadworks\AdvancedReviews\Model\Agreements\Checker as AgreementsChecker;

/**
 * Class OrderItem
 *
 * @package Aheadworks\AdvancedReviews\Model\Data\Processor\Review
 */
class OrderItem implements ProcessorInterface
{
    /**
     * @var OrderItemRepositoryInterface
     */
    private $orderItemRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var AgreementsChecker
     */
    private $agreementsChecker;

    /**
     * @param OrderItemRepositoryInterface $orderItemRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param AgreementsChecker $agreementsChecker
     */
    public function __construct(
        OrderItemRepositoryInterface $orderItemRepository,
        OrderRepositoryInterface $orderRepository,
        AgreementsChecker $agreementsChecker
    ) {
        $this->orderItemRepository = $orderItemRepository;
        $this->orderRepository = $orderRepository;
        $this->agreementsChecker = $agreementsChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        if (isset($data[ReviewInterface::ORDER_ITEM_ID])) {
            $orderItem = $this->getOrderItem($data[ReviewInterface::ORDER_ITEM_ID]);
            $order = $this->getOrder($orderItem);
            $data[ReviewInterface::CUSTOMER_ID] = $order->getCustomerId();
            if (empty($order->getCustomerId())) {
                $data[ReviewInterface::EMAIL] = $order->getCustomerEmail();
            }
            $data[ReviewInterface::PRODUCT_ID] = $orderItem->getProductId();
            $data[ReviewInterface::STORE_ID] = $orderItem->getStoreId();
            $data[ReviewInterface::IS_VERIFIED_BUYER] = ReviewIsVerifiedBuyerSource::YES;
            if ($this->agreementsChecker->areAgreementsEnabled($data[ReviewInterface::STORE_ID])) {
                $data[ReviewInterface::ARE_AGREEMENTS_CONFIRMED] = true;
            }
        }
        return $data;
    }

    /**
     * Retrieve order item by id
     *
     * @param int $orderItemId
     * @return OrderItemInterface
     */
    private function getOrderItem($orderItemId)
    {
        return $this->orderItemRepository->get($orderItemId);
    }

    /**
     * Retrieve order by order item
     *
     * @param OrderItemInterface $orderItem
     * @return OrderInterface
     */
    private function getOrder($orderItem)
    {
        return $this->orderRepository->get($orderItem->getOrderId());
    }
}
