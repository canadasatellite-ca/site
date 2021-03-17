<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Product\Layout\Processor\Review\Form;

use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Layout\LayoutProcessorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Config\Model\Config\Source\Nooptreq as NooptreqSource;

/**
 * Class EmailField
 *
 * @package Aheadworks\AdvancedReviews\Model\Product\Layout\Processor\Review\Form
 */
class EmailField implements LayoutProcessorInterface
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
     * @param ArrayManager $arrayManager
     * @param Config $config
     */
    public function __construct(
        ArrayManager $arrayManager,
        Config $config
    ) {
        $this->arrayManager = $arrayManager;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout, $productId = null, $storeId = null)
    {
        if ($this->isNeedToAddEmailField($storeId)) {
            $reviewFormChildrenPath = 'components/awArReviewContainer/children/awArReviewForm/children';
            $jsLayout = $this->arrayManager->merge(
                $reviewFormChildrenPath,
                $jsLayout,
                [
                    'email' => [
                        'component' => 'Magento_Ui/js/form/element/abstract',
                        'dataScope' => 'email',
                        'provider' => 'awArReviewFormProvider',
                        'configProvider' => 'awArReviewConfigProvider',
                        'template' => 'Aheadworks_AdvancedReviews/ui/form/custom-notice-field',
                        'elementTmpl' => 'ui/form/element/email',
                        'label' => __('Email'),
                        'notice' => __('(will not be published)'),
                        'sortOrder' => 25,
                        'imports' => [
                            'visible' => '!${ $.configProvider }:data.is_customer_logged_in'
                        ],
                        'validation' => [
                            'validate-email' => true,
                            'required-entry' => $this->isEmailFieldRequired($storeId),
                        ],
                    ]
                ]
            );
        }

        return $jsLayout;
    }

    /**
     * Check if need to add email field to the review submit form
     *
     * @param int|null $storeId
     * @return bool
     */
    private function isNeedToAddEmailField($storeId)
    {
        return $this->config->getDisplayModeOfEmailFieldForGuest($storeId) == NooptreqSource::VALUE_OPTIONAL
            || $this->config->getDisplayModeOfEmailFieldForGuest($storeId) == NooptreqSource::VALUE_REQUIRED;
    }

    /**
     * Check if email field is required
     *
     * @param int|null $storeId
     * @return bool
     */
    private function isEmailFieldRequired($storeId)
    {
        return $this->config->getDisplayModeOfEmailFieldForGuest($storeId) === NooptreqSource::VALUE_REQUIRED;
    }
}
