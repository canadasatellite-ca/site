<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Review;

use Aheadworks\AdvancedReviews\Model\Captcha\CaptchaAdapterInterface;
use Aheadworks\AdvancedReviews\Model\Config;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\AdvancedReviews\Model\Data\ProcessorInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterfaceFactory;
use Aheadworks\AdvancedReviews\Api\ReviewManagementInterface;
use Aheadworks\AdvancedReviews\Model\Captcha\Factory as CaptchaFactory;

/**
 * Class Save
 *
 * @package Aheadworks\AdvancedReviews\Controller\Review
 */
class Submit extends AbstractPostAction
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
     * @param Context $context
     * @param FormKeyValidator $formKeyValidator
     * @param CaptchaFactory $captchaFactory
     * @param Config $config
     * @param ProcessorInterface $reviewPostDataProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param ReviewInterfaceFactory $reviewInterfaceFactory
     * @param ReviewManagementInterface $reviewManagement
     */
    public function __construct(
        Context $context,
        FormKeyValidator $formKeyValidator,
        CaptchaFactory $captchaFactory,
        Config $config,
        ProcessorInterface $reviewPostDataProcessor,
        DataObjectHelper $dataObjectHelper,
        ReviewInterfaceFactory $reviewInterfaceFactory,
        ReviewManagementInterface $reviewManagement
    ) {
        parent::__construct($context, $formKeyValidator, $captchaFactory, $config);
        $this->reviewPostDataProcessor = $reviewPostDataProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->reviewInterfaceFactory = $reviewInterfaceFactory;
        $this->reviewManagement = $reviewManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $postData = $this->getRequest()->getPostValue();
        if (!empty($postData)) {
            try {
                $this->validate(CaptchaAdapterInterface::REVIEW_FORM_ID);
                $preparedData = $this->reviewPostDataProcessor->process($postData);
                $this->performSubmit($preparedData);
                $this->messageManager->addSuccessMessage($this->getSuccessMessage());
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while submitting the review.'));
            }
        }
        return $this->getPreparedRedirect();
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

    /**
     * Retrieve success message
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage()
    {
        return $this->config->getReviewPostSuccessMessage();
    }
}
