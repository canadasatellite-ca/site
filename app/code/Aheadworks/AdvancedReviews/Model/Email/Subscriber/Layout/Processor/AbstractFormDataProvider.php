<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\Subscriber\Layout\Processor;

use Aheadworks\AdvancedReviews\Model\Layout\LayoutProcessorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\AdvancedReviews\Model\Data\Extractor as DataExtractor;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface;
use Aheadworks\AdvancedReviews\Model\Config;

/**
 * Class AbstractFormDataProvider
 *
 * @package Aheadworks\AdvancedReviews\Model\Email\Subscriber\Layout\Processor
 * @codeCoverageIgnore
 */
abstract class AbstractFormDataProvider implements LayoutProcessorInterface
{
    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var DataExtractor
     */
    protected $dataExtractor;

    /**
     * @param ArrayManager $arrayManager
     * @param DataObjectProcessor $dataObjectProcessor
     * @param Config $config
     * @param DataExtractor $dataExtractor
     */
    public function __construct(
        ArrayManager $arrayManager,
        DataObjectProcessor $dataObjectProcessor,
        Config $config,
        DataExtractor $dataExtractor
    ) {
        $this->arrayManager = $arrayManager;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->config = $config;
        $this->dataExtractor = $dataExtractor;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout, $productId = null, $storeId = null)
    {
        $emailSubscriberFormProviderPath = $this->getSubscriberFormProviderPath();
        $jsLayout = $this->arrayManager->merge(
            $emailSubscriberFormProviderPath,
            $jsLayout,
            [
                'data' => $this->getPreparedSubscriberData($this->getCurrentSubscriber()),
                'config' => $this->getSubscriberFormConfig(),
            ]
        );

        return $jsLayout;
    }

    /**
     * Retrieve path to the subscriber form provider component
     *
     * @return string
     */
    abstract protected function getSubscriberFormProviderPath();

    /**
     * Retrieve data array for specific email subscriber
     *
     * @param SubscriberInterface|null $subscriber
     * @return array
     */
    protected function getPreparedSubscriberData($subscriber)
    {
        $preparedSubscriberData = [];
        if ($subscriber) {
            $subscriberData = $this->dataObjectProcessor->buildOutputDataArray(
                $subscriber,
                SubscriberInterface::class
            );
            $preparedSubscriberData = $this->dataExtractor->extractFields($subscriberData);
        }
        return $preparedSubscriberData;
    }

    /**
     * Retrieve current email subscriber
     *
     * @return SubscriberInterface|null
     */
    abstract protected function getCurrentSubscriber();

    /**
     * Retrieve config array for subscriber form
     *
     * @return array
     */
    protected function getSubscriberFormConfig()
    {
        return [
            'is_auto_approve_reviews_enabled' => $this->config->isAutoApproveReviewsEnabled(),
        ];
    }
}
