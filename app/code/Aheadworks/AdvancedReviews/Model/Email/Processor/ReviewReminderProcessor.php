<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\Processor;

use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Aheadworks\AdvancedReviews\Model\Source\Email\Variables as EmailVariables;
use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Email\EmailMetadataInterfaceFactory;
use Aheadworks\AdvancedReviews\Model\Review\Rating\Resolver;
use Aheadworks\AdvancedReviews\Model\Email\UrlBuilder;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\AdvancedReviews\Model\Email\QueueItem\SecurityCode\Generator as SecurityCodeGenerator;

/**
 * Class ReviewReminderProcessor
 * @package Aheadworks\AdvancedReviews\Model\Email\Processor
 */
class ReviewReminderProcessor extends AbstractUnsubscribeProcessor
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param EmailMetadataInterfaceFactory $emailMetadataFactory
     * @param UrlBuilder $urlBuilder
     * @param Resolver $resolver
     * @param SecurityCodeGenerator $securityCodeGenerator
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        EmailMetadataInterfaceFactory $emailMetadataFactory,
        UrlBuilder $urlBuilder,
        Resolver $resolver,
        SecurityCodeGenerator $securityCodeGenerator,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct(
            $config,
            $storeManager,
            $emailMetadataFactory,
            $urlBuilder,
            $resolver,
            $securityCodeGenerator
        );
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTemplateId($storeId)
    {
        return $this->config->getReviewReminderTemplate($storeId);
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareTemplateVariables(QueueItemInterface $queueItem)
    {
        $unsubscribeUrl = $this->generateUnsubscribeUrl($queueItem, $queueItem->getStoreId());

        return [
            EmailVariables::STORE => $this->storeManager->getStore($queueItem->getStoreId()),
            EmailVariables::ORDER => $this->orderRepository->get($queueItem->getObjectId()),
            EmailVariables::CUSTOMER_NAME => $queueItem->getRecipientName(),
            EmailVariables::UNSUBSCRIBE_URL => $unsubscribeUrl,
        ];
    }
}
