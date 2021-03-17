<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Block\Adminhtml\Review\Edit\Button;

use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Ui\Component\Control\Container;
use Magento\Backend\Block\Widget\Context;

/**
 * Class Save
 * @package Aheadworks\AdvancedReviews\Block\Adminhtml\Review\Edit\Button
 */
class Save implements ButtonProviderInterface
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        if ($this->context->getRequest()->getParam(ReviewInterface::ID, false)) {
            $buttonConfig = $this->getUpdateButtonConfigData();
        } else {
            $buttonConfig = $this->getCreateButtonConfigData();
        }
        return $buttonConfig;
    }

    /**
     * Retrieve update button config data
     *
     * @return array
     */
    private function getUpdateButtonConfigData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'aw_advanced_reviews_review_form.aw_advanced_reviews_review_form',
                                'actionName' => 'save',
                                'params' => [
                                    false
                                ]
                            ]
                        ]
                    ]
                ],
            ],
            'sort_order' => 50,
            'class_name' => Container::SPLIT_BUTTON,
            'options' => $this->getUpdateButtonOptions(),
        ];
    }

    /**
     * Retrieve options for update button
     *
     * @return array
     */
    private function getUpdateButtonOptions()
    {
        $options[] = [
            'id_hard' => 'ignore_all',
            'label' => __('Save & Ignore All Abuse Reports'),
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'aw_advanced_reviews_review_form.aw_advanced_reviews_review_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    [
                                        'ignore_all' => true
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];

        return $options;
    }

    /**
     * Retrieve create button config data
     *
     * @return array
     */
    private function getCreateButtonConfigData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => [],
                'form-role' => 'save'
            ],
            'sort_order' => 50,
            'class_name' => Container::DEFAULT_CONTROL
        ];
    }
}
