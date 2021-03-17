<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\Processor;

use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Email\EmailMetadataInterface;
use Aheadworks\AdvancedReviews\Model\Email\EmailMetadataInterfaceFactory;
use Aheadworks\AdvancedReviews\Model\Email\UrlBuilder;
use Aheadworks\AdvancedReviews\Model\Review\Rating\Resolver as RatingResolver;
use Magento\Framework\App\Area;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class AbstractProcessor
 * @package Aheadworks\AdvancedReviews\Model\Email\Processor
 */
abstract class AbstractProcessor implements MetadataProcessorInterface
{
    /**
     * Absolute calculated rating for email
     */
    const ABSOLUTE_RATING = 'absolute_rating';

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var EmailMetadataInterfaceFactory
     */
    protected $emailMetadataFactory;

    /**
     * @var UrlBuilder
     */
    protected $urlBuilder;

    /**
     * @var RatingResolver
     */
    protected $ratingResolver;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param EmailMetadataInterfaceFactory $emailMetadataFactory
     * @param UrlBuilder $urlBuilder
     * @param RatingResolver $ratingResolver
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        EmailMetadataInterfaceFactory $emailMetadataFactory,
        UrlBuilder $urlBuilder,
        RatingResolver $ratingResolver
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->emailMetadataFactory = $emailMetadataFactory;
        $this->urlBuilder = $urlBuilder;
        $this->ratingResolver = $ratingResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function process($queueItem)
    {
        $storeId = $queueItem->getStoreId();
        /** @var EmailMetadataInterface $emailMetaData */
        $emailMetaData = $this->emailMetadataFactory->create();
        $emailMetaData
            ->setTemplateId($this->getTemplateId($storeId))
            ->setTemplateOptions($this->getTemplateOptions($storeId))
            ->setTemplateVariables($this->prepareTemplateVariables($queueItem))
            ->setSenderName($this->getSenderName($storeId))
            ->setSenderEmail($this->getSenderEmail($storeId))
            ->setRecipientName($this->getRecipientName($queueItem))
            ->setRecipientEmail($this->getRecipientEmail($queueItem));

        return $emailMetaData;
    }

    /**
     * Retrieve template id
     *
     * @param int $storeId
     * @return string
     */
    abstract protected function getTemplateId($storeId);

    /**
     * Prepare template options
     *
     * @param int $storeId
     * @return array
     */
    protected function getTemplateOptions($storeId)
    {
        return [
            'area' => Area::AREA_FRONTEND,
            'store' => $storeId
        ];
    }

    /**
     * Prepare template variables
     *
     * @param QueueItemInterface $queueItem
     * @return array
     */
    abstract protected function prepareTemplateVariables(QueueItemInterface $queueItem);

    /**
     * Retrieve sender name
     *
     * @param int $storeId
     * @return string
     * @throws \Magento\Framework\Exception\MailException
     */
    protected function getSenderName($storeId)
    {
        return $this->config->getSenderName($storeId);
    }

    /**
     * Retrieve sender email
     *
     * @param int $storeId
     * @return string
     * @throws \Magento\Framework\Exception\MailException
     */
    protected function getSenderEmail($storeId)
    {
        return $this->config->getSenderEmail($storeId);
    }

    /**
     * Retrieve recipient name
     *
     * @param QueueItemInterface $queueItem
     * @return string
     */
    protected function getRecipientName($queueItem)
    {
        return $queueItem->getRecipientName();
    }

    /**
     * Retrieve recipient email
     *
     * @param QueueItemInterface $queueItem
     * @return string
     */
    protected function getRecipientEmail($queueItem)
    {
        return $queueItem->getRecipientEmail();
    }

    /**
     * Prepare review rating
     *
     * @param ReviewInterface $review
     */
    protected function prepareReviewRating(ReviewInterface $review)
    {
        $absoluteRating = $this->ratingResolver->getRatingAbsoluteValue($review->getRating(), 0);
        $review->setData(self::ABSOLUTE_RATING, $absoluteRating);
    }
}
