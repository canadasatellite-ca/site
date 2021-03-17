<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Product\Layout\Processor\Review\Form;

use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Layout\LayoutProcessorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\AdvancedReviews\Model\Store\Resolver as StoreResolver;

/**
 * Class ProsAndConsFields
 *
 * @package Aheadworks\AdvancedReviews\Model\Product\Layout\Processor\Review\Form
 */
class ProsAndConsFields implements LayoutProcessorInterface
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
     * @var StoreResolver
     */
    private $storeResolver;

    /**
     * @param ArrayManager $arrayManager
     * @param Config $config
     * @param StoreResolver $storeResolver
     */
    public function __construct(
        ArrayManager $arrayManager,
        Config $config,
        StoreResolver $storeResolver
    ) {
        $this->arrayManager = $arrayManager;
        $this->config = $config;
        $this->storeResolver = $storeResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout, $productId = null, $storeId = null)
    {
        if ($this->isNeedToAddProsAndConsFields($storeId)) {
            $reviewFormChildrenPath = 'components/awArReviewContainer/children/awArReviewForm/children';
            $jsLayout = $this->arrayManager->merge(
                $reviewFormChildrenPath,
                $jsLayout,
                [
                    'pros' => [
                        'component' => 'Magento_Ui/js/form/element/textarea',
                        'dataScope' => 'pros',
                        'provider' => 'awArReviewFormProvider',
                        'template' => 'ui/form/field',
                        'label' => __('Advantages'),
                        'placeholder' => __('Tell others what you like about the product'),
                        'sortOrder' => 35,
                    ],
                    'cons' => [
                        'component' => 'Magento_Ui/js/form/element/textarea',
                        'dataScope' => 'cons',
                        'provider' => 'awArReviewFormProvider',
                        'template' => 'ui/form/field',
                        'label' => __('Disadvantages'),
                        'placeholder' => __('Tell others what you don\'t like about the product'),
                        'sortOrder' => 36,
                    ],
                    'content' => [
                        'label' => __('Comment'),
                        'placeholder' => __('Add any other thoughts which you want to share with others'),
                    ],
                ]
            );
        }

        return $jsLayout;
    }

    /**
     * Check if need to add pros and cons fields to the review submit form
     *
     * @param int|null $storeId
     * @return bool
     */
    private function isNeedToAddProsAndConsFields($storeId)
    {
        $websiteId = $this->storeResolver->getWebsiteIdByStoreId($storeId);
        return $this->config->areProsAndConsEnabled($websiteId);
    }
}
