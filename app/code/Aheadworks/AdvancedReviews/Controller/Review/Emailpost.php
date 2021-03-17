<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Review;

use Aheadworks\AdvancedReviews\Api\ReviewRepositoryInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\AdvancedReviews\Model\Data\ProcessorInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterfaceFactory;
use Aheadworks\AdvancedReviews\Api\ReviewManagementInterface;
use Magento\Integration\Controller\Token\Request;
use Magento\Framework\Oauth\OauthInterface;
use Magento\Framework\Oauth\Helper\Request as RequestHelper;
use Aheadworks\AdvancedReviews\Model\Config;

/**
 * Class Emailpost
 * @package Aheadworks\AdvancedReviews\Controller\Review
 */
class Emailpost extends Request
{
    /**
     * @var ProcessorInterface
     */
    private $reviewPostDataProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var ReviewInterfaceFactory
     */
    private $reviewInterfaceFactory;

    /**
     * @var ReviewManagementInterface
     */
    private $reviewManagement;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var ReviewRepositoryInterface
     */
    private $reviewRepository;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Context $context
     * @param OauthInterface $oauthService
     * @param RequestHelper $helper
     * @param ProcessorInterface $reviewPostDataProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param ReviewInterfaceFactory $reviewInterfaceFactory
     * @param ReviewManagementInterface $reviewManagement
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ReviewRepositoryInterface $reviewRepository
     * @param Config $config
     */
    public function __construct(
        Context $context,
        OauthInterface $oauthService,
        RequestHelper $helper,
        ProcessorInterface $reviewPostDataProcessor,
        DataObjectHelper $dataObjectHelper,
        ReviewInterfaceFactory $reviewInterfaceFactory,
        ReviewManagementInterface $reviewManagement,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ReviewRepositoryInterface $reviewRepository,
        Config $config
    ) {
        parent::__construct($context, $oauthService, $helper);
        $this->reviewPostDataProcessor = $reviewPostDataProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->reviewInterfaceFactory = $reviewInterfaceFactory;
        $this->reviewManagement = $reviewManagement;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->reviewRepository = $reviewRepository;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $postData = $this->getRequest()->getPostValue();
        $isValid = $this->validateData($postData);
        if ($isValid) {
            if ($this->isReviewAlreadySubmitted($postData[ReviewInterface::ORDER_ITEM_ID])) {
                $this->messageManager->addErrorMessage(
                    __('We are sorry, you can\'t submit a review on the same product twice.')
                );
                return $this->getPreparedRedirect();
            }
            try {
                $preparedData = $this->reviewPostDataProcessor->process($postData);
                $this->performSubmit($preparedData);
                $this->messageManager->addSuccessMessage($this->config->getReviewPostSuccessMessage());
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while submitting the review.'));
            }
        }
        return $this->getPreparedRedirect();
    }

    /**
     * Validate required data
     *
     * @param array $data
     * @return bool
     */
    private function validateData(array $data)
    {
        $isValid = true;

        if (empty($data)) {
            $this->messageManager->addErrorMessage(__('We are sorry, we can\'t publish an empty review.'));
            return false;
        }

        if (!isset($data[ReviewInterface::ORDER_ITEM_ID]) || empty($data[ReviewInterface::ORDER_ITEM_ID])) {
            $this->messageManager->addErrorMessage(
                __('Please get back to the email and specify the product which you were reviewed.')
            );
            $isValid = false;
        }
        if (!isset($data[ReviewInterface::RATING]) || empty($data[ReviewInterface::RATING])) {
            $this->messageManager->addErrorMessage(
                __('Please get back to the email and specify the rating of the product which you were reviewed.')
            );
            $isValid = false;
        }
        if (!isset($data[ReviewInterface::CONTENT]) || empty($data[ReviewInterface::CONTENT])) {
            $this->messageManager->addErrorMessage(
                __('Please get back to the email and write a few words about the product!')
            );
            $isValid = false;
        }

        return $isValid;
    }

    /**
     * Check is review already submitted
     *
     * @param $orderItemId
     * @return int
     */
    private function isReviewAlreadySubmitted($orderItemId)
    {
        $this->searchCriteriaBuilder->addFilter(ReviewInterface::ORDER_ITEM_ID, $orderItemId);
        $result = $this->reviewRepository->getList($this->searchCriteriaBuilder->create());

        return count($result->getItems());
    }

    /**
     * Perform review submit
     *
     * @param array $preparedData
     * @return ReviewInterface
     * @throws LocalizedException
     */
    private function performSubmit($preparedData)
    {
        $reviewObject = $this->reviewInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $reviewObject,
            $preparedData,
            ReviewInterface::class
        );
        return $this->reviewManagement->createReview($reviewObject);
    }

    /**
     * Retrieve redirect to the current product page
     *
     * @return Redirect
     */
    protected function getPreparedRedirect()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setRefererOrBaseUrl();
    }
}
