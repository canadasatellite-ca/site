<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Block\Product\View\Review;

use Aheadworks\AdvancedReviews\Model\Captcha\CaptchaAdapterInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\AdvancedReviews\Model\Layout\LayoutProcessorProviderInterface;
use Aheadworks\AdvancedReviews\Model\Product\Resolver as ProductResolver;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\AdvancedReviews\Api\StatisticsRepositoryInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Aheadworks\AdvancedReviews\Model\Captcha\Factory as CaptchaFactory;

/**
 * Class Container
 *
 * @package Aheadworks\AdvancedReviews\Block\Product\View\Review
 */
class Container extends Template implements IdentityInterface
{
    /**
     * @var LayoutProcessorProviderInterface
     */
    private $layoutProcessorProvider;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var CaptchaFactory
     */
    private $captchaFactory;

    /**
     * @var int
     */
    private $currentProductId;

    /**
     * @var int
     */
    private $currentStoreId;

    /**
     * @param Context $context
     * @param LayoutProcessorProviderInterface $layoutProcessorProvider
     * @param ProductResolver $productResolver
     * @param StoreManagerInterface $storeManager
     * @param StatisticsRepositoryInterface $statisticsRepository
     * @param Json $serializer
     * @param CaptchaFactory $captchaFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        LayoutProcessorProviderInterface $layoutProcessorProvider,
        ProductResolver $productResolver,
        StoreManagerInterface $storeManager,
        StatisticsRepositoryInterface $statisticsRepository,
        Json $serializer,
        CaptchaFactory $captchaFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->layoutProcessorProvider = $layoutProcessorProvider;
        $this->serializer = $serializer;
        $this->captchaFactory = $captchaFactory;
        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout'])
            ? $data['jsLayout']
            : [];

        $this->currentProductId = $productResolver->getCurrentProductId();
        $this->currentStoreId = $storeManager->getStore(true)->getId();

        if ($this->isNeedToRenderContainer()) {
            $this->addTitle($statisticsRepository);
        }
    }

    /**
     * Check if parameters, required for correct rendering of the container, are specified
     *
     * @return bool
     */
    public function isNeedToRenderContainer()
    {
        return (isset($this->currentProductId) && isset($this->currentStoreId));
    }

    /**
     * {@inheritdoc}
     */
    public function getJsLayout()
    {
        foreach ($this->layoutProcessorProvider->getLayoutProcessors() as $layoutProcessor) {
            $this->jsLayout = $layoutProcessor->process(
                $this->jsLayout,
                $this->currentProductId,
                $this->currentStoreId
            );
        }

        return \Zend_Json::encode($this->jsLayout);
    }

    /**
     * Retrieve serialized config
     *
     * @return string
     */
    public function getSerializedConfig()
    {
        $config = [];
        $reviewCaptcha = $this->captchaFactory->create(CaptchaAdapterInterface::REVIEW_FORM_ID);
        if ($reviewCaptcha && $reviewCaptcha->isEnabled()) {
            $config = array_merge($config, $reviewCaptcha->getConfigData());
        }
        $commentCaptcha = $this->captchaFactory->create(CaptchaAdapterInterface::COMMENT_FORM_ID_BASE);
        if ($commentCaptcha && $commentCaptcha->isEnabled()) {
            $config = array_merge($config, $commentCaptcha->getConfigData());
        }
        return $this->serializer->serialize($config);
    }

    /**
     * Add tab title with total reviews count
     *
     * @param StatisticsRepositoryInterface $statisticsRepository
     * @return $this
     */
    private function addTitle($statisticsRepository)
    {
        $totalReviewsCount = $this->getTotalReviewsCount($statisticsRepository);
        $title = $this->generateTitle($totalReviewsCount);
        $this->setData('title', $title);
        return $this;
    }

    /**
     * Retrieve total count of reviews to display
     *
     * @param StatisticsRepositoryInterface $statisticsRepository
     * @return int
     */
    private function getTotalReviewsCount($statisticsRepository)
    {
        $totalReviewsCount = 0;
        if (isset($this->currentProductId)) {
            $statisticsInstance = $statisticsRepository->getByProductId(
                $this->currentProductId,
                $this->currentStoreId
            );
            $totalReviewsCount = $statisticsInstance->getReviewsCount();
        }
        return $totalReviewsCount;
    }

    /**
     * Retrieve title of the tab based on the current reviews count
     *
     * @param int $totalReviewsCount
     * @return string
     */
    private function generateTitle($totalReviewsCount)
    {
        return $totalReviewsCount
            ? __('Reviews %1', '<span class="counter">' . $totalReviewsCount . '</span>')
            : __('Reviews');
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        $identities = [];
        $identities = array_merge($identities, $this->getContainerIdentities());
        return $identities;
    }

    /**
     * Retrieve current container identities
     *
     * @return array
     */
    protected function getContainerIdentities()
    {
        $containerIdentities = [];
        if ($this->isNeedToRenderContainer()) {
            $containerIdentities[] = ReviewInterface::CACHE_PRODUCT_TAG . '_' . $this->currentProductId;
        }
        return $containerIdentities;
    }
}
