<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Product\Layout\Processor\Review;

use Aheadworks\AdvancedReviews\Model\Layout\LayoutProcessorInterface;
use Aheadworks\AdvancedReviews\Model\Source\Review\Comment\Type;
use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\AdvancedReviews\Model\Config;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Customer\Model\Url as CustomerUrl;
use Aheadworks\AdvancedReviews\Model\Source\Review\RatingValue as ReviewRatingValueSource;
use Aheadworks\AdvancedReviews\Model\Source\Review\ProductRecommended as ProductRecommendedSource;
use Aheadworks\AdvancedReviews\Api\StatisticsRepositoryInterface;
use Aheadworks\AdvancedReviews\Model\Product\Layout\Processor\Review\Form\AgreementsConfig
    as ReviewFormAgreementsConfig;

/**
 * Class ConfigProvider
 *
 * @package Aheadworks\AdvancedReviews\Model\Product\Layout\Processor\Review
 */
class ConfigDataProvider implements LayoutProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var CustomerUrl
     */
    private $customerUrl;

    /**
     * @var ReviewRatingValueSource
     */
    private $reviewRatingValueSource;

    /**
     * @var ProductRecommendedSource
     */
    private $productRecommendedSource;

    /**
     * @var StatisticsRepositoryInterface
     */
    private $statisticsRepository;

    /**
     * @var ReviewFormAgreementsConfig
     */
    private $reviewFormAgreementsConfig;

    /**
     * @param ArrayManager $arrayManager
     * @param Config $config
     * @param HttpContext $httpContext
     * @param CustomerUrl $customerUrl
     * @param ReviewRatingValueSource $reviewRatingValueSource
     * @param ProductRecommendedSource $productRecommendedSource
     * @param StatisticsRepositoryInterface $statisticsRepository
     * @param ReviewFormAgreementsConfig $reviewFormAgreementsConfig
     */
    public function __construct(
        ArrayManager $arrayManager,
        Config $config,
        HttpContext $httpContext,
        CustomerUrl $customerUrl,
        ReviewRatingValueSource $reviewRatingValueSource,
        ProductRecommendedSource $productRecommendedSource,
        StatisticsRepositoryInterface $statisticsRepository,
        ReviewFormAgreementsConfig $reviewFormAgreementsConfig
    ) {
        $this->arrayManager = $arrayManager;
        $this->config = $config;
        $this->httpContext = $httpContext;
        $this->customerUrl = $customerUrl;
        $this->reviewRatingValueSource = $reviewRatingValueSource;
        $this->productRecommendedSource = $productRecommendedSource;
        $this->statisticsRepository = $statisticsRepository;
        $this->reviewFormAgreementsConfig = $reviewFormAgreementsConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout, $productId = null, $storeId = null)
    {
        $reviewFormProviderPath = 'components/awArReviewConfigProvider';
        $jsLayout = $this->arrayManager->merge(
            $reviewFormProviderPath,
            $jsLayout,
            [
                'data' => array_merge_recursive(
                    $this->getGeneralConfigData($productId, $storeId),
                    [
                        'review_form' => $this->getReviewFormConfigData($storeId)
                    ],
                    [
                        'review_list' => $this->getReviewListConfigData($productId)
                    ]
                )
            ]
        );

        return $jsLayout;
    }

    /**
     * Retrieve general config data
     *
     * @param int|null $productId
     * @param int|null $storeId
     * @return array
     */
    private function getGeneralConfigData($productId = null, $storeId = null)
    {
        return [
            'is_customer_logged_in' => $this->httpContext->getValue(CustomerContext::CONTEXT_AUTH),
            'register_url' => $this->customerUrl->getRegisterUrl(),
            'login_url' => $this->customerUrl->getLoginUrl(),
            'total_reviews_count' => $this->getTotalReviewsCount($productId, $storeId),
        ];
    }

    /**
     * Retrieve total count of reviews to display
     *
     * @param int|null $productId
     * @param int|null $storeId
     * @return int
     */
    private function getTotalReviewsCount($productId = null, $storeId = null)
    {
        $statisticsInstance = $this->statisticsRepository->getByProductId($productId, $storeId);
        $totalReviewsCount = $statisticsInstance->getReviewsCount();
        return $totalReviewsCount;
    }

    /**
     * Retrieve config data for review form
     *
     * @param int|null $storeId
     * @return array
     */
    private function getReviewFormConfigData($storeId)
    {
        return [
            'is_allow_guest_submit_review' => $this->config->isAllowGuestSubmitReview($storeId),
            'rating_options' => $this->reviewRatingValueSource->toOptionArray(),
            'product_recommended_options' => $this->productRecommendedSource->toOptionArray(),
            'agreements_config' => $this->reviewFormAgreementsConfig->getConfigData($storeId),
        ];
    }

    /**
     * Retrieve config data for review list
     *
     * @param int|null $productId
     * @return array
     */
    private function getReviewListConfigData($productId = null)
    {
        $reviewListConfigData = [
            'comment_form_config' => $this->getCommentFormJsConfig()
        ];
        if (isset($productId)) {
            $reviewListConfigData['product_id'] = $productId;
        }

        return $reviewListConfigData;
    }

    /**
     * Retrieve comment form js config
     *
     * @return array
     */
    private function getCommentFormJsConfig()
    {
        $providerName = 'awArCommentFormProvider';

        return [
            'component' => 'Aheadworks_AdvancedReviews/js/product/view/review/comment/form',
            'provider' => $providerName,
            'deps' => [$providerName, 'awArReviewConfigProvider'],
            'dataFormPartSelectors' => ['[name=captcha_string]', '[name=g-recaptcha-response]'],
            'ajaxSave' => true,
            'children' => [
                'type' => [
                    'name' => 'type',
                    'component'=>
                        'Aheadworks_AdvancedReviews/js/product/view/review/comment/form/element/static-value',
                    'dataScope' => 'type',
                    'provider' => $providerName,
                    'template' => 'ui/form/field',
                    'visible' => false,
                    'value' => Type::VISITOR,
                ],
                'review_id' => [
                    'name' => 'review_id',
                    'component' =>
                        'Aheadworks_AdvancedReviews/js/product/view/review/comment/form/element/static-value',
                    'dataScope' => 'review_id',
                    'provider' => $providerName,
                    'template' => 'ui/form/field',
                    'visible' => false,
                ],
                'nickname' => [
                    'name' => 'nickname',
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'dataScope' => 'nickname',
                    'provider' => $providerName,
                    'template' => 'ui/form/field',
                    'label' => __('Nickname'),
                    'visible' => true,
                    'validation' => ['required-entry' => true]
                ],
                'content' => [
                    'name' => 'content',
                    'component' => 'Magento_Ui/js/form/element/textarea',
                    'dataScope' => 'content',
                    'provider' => $providerName,
                    'template' => 'ui/form/field',
                    'label' => __('Comment'),
                    'visible' => true,
                    'validation' => ['required-entry' => true]
                ]
            ]
        ];
    }
}