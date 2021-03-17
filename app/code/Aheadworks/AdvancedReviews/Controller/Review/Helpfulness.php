<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Review;

use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;
use Aheadworks\AdvancedReviews\Api\HelpfulnessManagementInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Helpfulness
 * @package Aheadworks\AdvancedReviews\Controller\Review
 */
class Helpfulness extends Action
{
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var HelpfulnessManagementInterface
     */
    private $helpfulnessManagement;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param HelpfulnessManagementInterface $helpfulnessManagement
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        HelpfulnessManagementInterface $helpfulnessManagement,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helpfulnessManagement = $helpfulnessManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $params = json_decode($this->getRequest()->getContent(), true);
        $reviewId = isset($params['reviewId']) ? $params['reviewId'] : null;
        $action = isset($params['action']) ? $params['action'] : '';
        $success = false;

        if ($reviewId) {
            try {
                $voteResult = $this->helpfulnessManagement->vote($reviewId, $action);
                $success = true;
            } catch (CouldNotSaveException $e) {
                $data = [
                    'success' => $success,
                    'message' => __('Something went wrong. Please vote again later.')
                ];
                return $this->resultJsonFactory->create()->setData($data);
            }

            $data = [
                'success' => $success,
                ReviewInterface::VOTES_POSITIVE => $voteResult->getLikesCount(),
                ReviewInterface::VOTES_NEGATIVE => $voteResult->getDislikesCount(),
                'message' => __('Thank you for voting!')
            ];
        } else {
            $data = [
                'success' => $success,
                'message' => __('Something went wrong.')
            ];
        }

        return $this->resultJsonFactory->create()->setData($data);
    }
}
