<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Adminhtml\Review;

use Aheadworks\AdvancedReviews\Api\AbuseReportManagementInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterfaceFactory;
use Aheadworks\AdvancedReviews\Model\Data\ProcessorInterface;
use Aheadworks\AdvancedReviews\Api\ReviewManagementInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Backend\App\Action;

/**
 * Class Save
 * @package Aheadworks\AdvancedReviews\Controller\Adminhtml\Review
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_AdvancedReviews::reviews';

    /**
     * @var ReviewInterfaceFactory
     */
    private $reviewDataFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var ProcessorInterface
     */
    private $postDataProcessor;

    /**
     * @var ReviewManagementInterface
     */
    private $reviewManagement;

    /**
     * @var AbuseReportManagementInterface
     */
    private $abuseReportManagement;

    /**
     * @param Context $context
     * @param ReviewInterfaceFactory $reviewDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     * @param ProcessorInterface $postDataProcessor
     * @param ReviewManagementInterface $reviewManagement
     * @param AbuseReportManagementInterface $abuseReportManagement
     */
    public function __construct(
        Context $context,
        ReviewInterfaceFactory $reviewDataFactory,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor,
        ProcessorInterface $postDataProcessor,
        ReviewManagementInterface $reviewManagement,
        AbuseReportManagementInterface $abuseReportManagement
    ) {
        parent::__construct($context);
        $this->reviewDataFactory = $reviewDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPersistor = $dataPersistor;
        $this->postDataProcessor = $postDataProcessor;
        $this->reviewManagement = $reviewManagement;
        $this->abuseReportManagement = $abuseReportManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($postData = $this->getRequest()->getPostValue()) {
            $back = $this->getRequest()->getParam('back');
            $ignoreAll = (bool)$this->getRequest()->getParam('ignore_all', false);
            try {
                $preparedData = $this->postDataProcessor->process($postData);
                $review = $this->performSave($preparedData);
                if ($this->isReviewAlreadyExist($preparedData)) {
                    $this->abuseReportManagement->ignoreAbuseForReviews([$review->getId()], $ignoreAll);
                }
                $this->dataPersistor->clear('aw_adv_rev_review_form');
                $this->messageManager->addSuccessMessage(__('The review was successfully saved.'));
                if ($back == 'edit') {
                    return $resultRedirect->setPath(
                        '*/*/' . $back,
                        [
                            ReviewInterface::ID => $review->getId(),
                            '_current' => true
                        ]
                    );
                }
                return $resultRedirect->setPath('*/*/');
            } catch (CouldNotSaveException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while saving the review.')
                );
            }
            $this->dataPersistor->set('aw_adv_rev_review_form', $preparedData);
            if ($this->isReviewAlreadyExist($preparedData)) {
                return $resultRedirect->setPath(
                    '*/*/edit',
                    [ReviewInterface::ID => $preparedData[ReviewInterface::ID], '_current' => true]
                );
            }
            return $resultRedirect->setPath('*/*/new', ['_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Perform review save
     *
     * @param array $preparedData
     * @return ReviewInterface
     * @throws CouldNotSaveException
     */
    private function performSave($preparedData)
    {
        $reviewObject = $this->reviewDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $reviewObject,
            $preparedData,
            ReviewInterface::class
        );

        if ($this->isReviewAlreadyExist($preparedData)) {
            $savedReview = $this->reviewManagement->updateReview($reviewObject);
        } else {
            $savedReview = $this->reviewManagement->createReview($reviewObject);
        }

        return $savedReview;
    }

    /**
     * Check if review already exists
     *
     * @param array $reviewData
     * @return bool
     */
    private function isReviewAlreadyExist($reviewData)
    {
        return isset($reviewData[ReviewInterface::ID]) && !empty($reviewData[ReviewInterface::ID]);
    }
}
