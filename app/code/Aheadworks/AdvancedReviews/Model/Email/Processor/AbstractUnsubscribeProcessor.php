<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\Processor;

use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Email\EmailMetadataInterfaceFactory;
use Aheadworks\AdvancedReviews\Model\Review\Rating\Resolver;
use Aheadworks\AdvancedReviews\Model\Email\UrlBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\AdvancedReviews\Model\Email\QueueItem\SecurityCode\Generator as SecurityCodeGenerator;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Layout\Processor\UnsubscribeLink\FormDataProvider;

/**
 * Class AbstractUnsubscribeProcessor
 *
 * @package Aheadworks\AdvancedReviews\Model\Email\Processor
 */
abstract class AbstractUnsubscribeProcessor extends AbstractProcessor
{
    /**
     * @var SecurityCodeGenerator
     */
    private $securityCodeGenerator;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param EmailMetadataInterfaceFactory $emailMetadataFactory
     * @param UrlBuilder $urlBuilder
     * @param Resolver $resolver
     * @param SecurityCodeGenerator $securityCodeGenerator
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        EmailMetadataInterfaceFactory $emailMetadataFactory,
        UrlBuilder $urlBuilder,
        Resolver $resolver,
        SecurityCodeGenerator $securityCodeGenerator
    ) {
        parent::__construct($config, $storeManager, $emailMetadataFactory, $urlBuilder, $resolver);
        $this->securityCodeGenerator = $securityCodeGenerator;
    }

    /**
     * Generate unsubscribe url
     *
     * @param QueueItemInterface $queueItem
     * @param string $scope
     * @return string
     * @throws LocalizedException
     */
    protected function generateUnsubscribeUrl(QueueItemInterface $queueItem, $scope)
    {
        $securityCode = $this->securityCodeGenerator->getCode();
        $queueItem->setSecurityCode($securityCode);
        $unsubscribeUrl = $this->urlBuilder->getFrontendUrl(
            'aw_advanced_reviews/subscriber/edit',
            $scope,
            [
                FormDataProvider::SECURITY_CODE_REQUEST_PARAM_KEY => $securityCode,
                '_scope_to_url' => true,
            ]
        );
        return $unsubscribeUrl;
    }
}
