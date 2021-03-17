<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\Processor;

use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Aheadworks\AdvancedReviews\Model\Source\Email\Variables as EmailVariables;
use Aheadworks\AdvancedReviews\Api\CommentRepositoryInterface;
use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Email\EmailMetadataInterfaceFactory;
use Aheadworks\AdvancedReviews\Model\Review\Rating\Resolver;
use Aheadworks\AdvancedReviews\Model\Email\UrlBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\AdvancedReviews\Model\Email\QueueItem\SecurityCode\Generator as SecurityCodeGenerator;

/**
 * Class ReviewCommentProcessor
 * @package Aheadworks\AdvancedReviews\Model\Email\Processor
 */
class ReviewCommentProcessor extends AbstractUnsubscribeProcessor
{
    /**
     * Template
     */
    const TEMPLATE = 'aw_advanced_reviews_email_review_comment_template';

    /**
     * @var CommentRepositoryInterface
     */
    private $commentRepository;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param EmailMetadataInterfaceFactory $emailMetadataFactory
     * @param UrlBuilder $urlBuilder
     * @param Resolver $resolver
     * @param SecurityCodeGenerator $securityCodeGenerator
     * @param CommentRepositoryInterface $commentRepository
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        EmailMetadataInterfaceFactory $emailMetadataFactory,
        UrlBuilder $urlBuilder,
        Resolver $resolver,
        SecurityCodeGenerator $securityCodeGenerator,
        CommentRepositoryInterface $commentRepository
    ) {
        parent::__construct(
            $config,
            $storeManager,
            $emailMetadataFactory,
            $urlBuilder,
            $resolver,
            $securityCodeGenerator
        );
        $this->commentRepository = $commentRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTemplateId($storeId)
    {
        return self::TEMPLATE;
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareTemplateVariables(QueueItemInterface $queueItem)
    {
        $comment = $this->commentRepository->getById($queueItem->getObjectId());
        $unsubscribeUrl = $this->generateUnsubscribeUrl($queueItem, $queueItem->getStoreId());

        return [
            EmailVariables::COMMENT => $comment,
            EmailVariables::CUSTOMER_NAME => $queueItem->getRecipientName(),
            EmailVariables::UNSUBSCRIBE_URL => $unsubscribeUrl,
        ];
    }
}
