<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Product\Layout\Processor\Review;

use Aheadworks\AdvancedReviews\Model\Captcha\CaptchaAdapterInterface;
use Aheadworks\AdvancedReviews\Model\Layout\LayoutProcessorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\AdvancedReviews\Model\Captcha\Factory as CaptchaFactory;

/**
 * Class Captcha
 * @package Aheadworks\AdvancedReviews\Model\Product\Layout\Processor\Review
 */
class Captcha implements LayoutProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var CaptchaFactory
     */
    private $captchaFactory;

    /**
     * @param ArrayManager $arrayManager
     * @param CaptchaFactory $captchaFactory
     */
    public function __construct(
        ArrayManager $arrayManager,
        CaptchaFactory $captchaFactory
    ) {
        $this->arrayManager = $arrayManager;
        $this->captchaFactory = $captchaFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout, $productId = null, $storeId = null)
    {
        $jsLayout = $this->addReviewFormCaptcha($jsLayout);
        $jsLayout = $this->addCommentFormCaptcha($jsLayout);

        return $jsLayout;
    }

    /**
     * Add review form captcha
     *
     * @param array $jsLayout
     * @return array
     */
    private function addReviewFormCaptcha($jsLayout)
    {
        $captcha = $this->captchaFactory->create(CaptchaAdapterInterface::REVIEW_FORM_ID);
        if ($captcha && $captcha->isEnabled()) {
            $reviewFormChildrenPath = 'components/awArReviewContainer/children/awArReviewForm/children';
            $jsLayout = $this->arrayManager->merge(
                $reviewFormChildrenPath,
                $jsLayout,
                [
                    'captcha' => array_merge(
                        [
                            'provider' => 'awArReviewFormProvider',
                            'dataScope' => 'captcha'
                        ],
                        $captcha->getLayoutConfig()
                    )
                ]
            );
        }

        return $jsLayout;
    }

    /**
     * Add comment form captcha
     *
     * @param array $jsLayout
     * @return array
     */
    private function addCommentFormCaptcha($jsLayout)
    {
        $captcha = $this->captchaFactory->create(CaptchaAdapterInterface::COMMENT_FORM_ID_BASE);
        if ($captcha && $captcha->isEnabled()) {
            $commentFormChildrenPath
                = 'components/awArReviewConfigProvider/data/review_list/comment_form_config/children';
            $jsLayout = $this->arrayManager->merge(
                $commentFormChildrenPath,
                $jsLayout,
                [
                    'captcha' => array_merge(
                        [
                            'provider' => 'awArCommentFormProvider',
                            'dataScope' => 'captcha',
                            'sortOrder' => 100
                        ],
                        $captcha->getLayoutConfig()
                    )
                ]
            );
        }

        return $jsLayout;
    }
}
