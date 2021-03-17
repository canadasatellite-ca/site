<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Block\Adminhtml\Review\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;
use Aheadworks\AdvancedReviews\Api\ReviewRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;

/**
 * Class Delete
 * @package Aheadworks\AdvancedReviews\Block\Adminhtml\Review\Edit\Button
 */
class Delete implements ButtonProviderInterface
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var ReviewRepositoryInterface
     */
    private $reviewRepository;

    /**
     * @param Context $context
     * @param ReviewRepositoryInterface $reviewRepository
     */
    public function __construct(
        Context $context,
        ReviewRepositoryInterface $reviewRepository
    ) {
        $this->context = $context;
        $this->reviewRepository = $reviewRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $data = [];
        $reviewId = $this->context->getRequest()->getParam('id');
        if ($reviewId) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => sprintf(
                    "deleteConfirm('%s', '%s')",
                    __('Are you sure you want to do this?'),
                    $this->getUrl('*/*/delete', [ReviewInterface::ID => $reviewId])
                ),
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
