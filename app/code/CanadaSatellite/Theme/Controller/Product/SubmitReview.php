<?php

namespace CanadaSatellite\Theme\Controller\Product;

use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterfaceFactory;
use Aheadworks\AdvancedReviews\Api\ReviewManagementInterface;
use Aheadworks\AdvancedReviews\Controller\Review\Submit;
use Aheadworks\AdvancedReviews\Model\Captcha\Factory as CaptchaFactory;
use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Data\ProcessorInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;

class SubmitReview extends Submit
{

    /**
     * @var ProcessorInterface
     */
    private $reviewPostDataProcessor;

    /**
     * @var ReviewInterfaceFactory
     */
    private $reviewInterfaceFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var ReviewManagementInterface
     */
    private $reviewManagement;

    public function __construct(
        Context $context,
        FormKeyValidator $formKeyValidator,
        CaptchaFactory $captchaFactory,
        Config $config,
        ProcessorInterface $reviewPostDataProcessor,
        DataObjectHelper $dataObjectHelper,
        ReviewInterfaceFactory $reviewInterfaceFactory,
        ReviewManagementInterface $reviewManagement)
    {
        $this->reviewPostDataProcessor = $reviewPostDataProcessor;
        $this->reviewInterfaceFactory = $reviewInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->reviewManagement = $reviewManagement;
        parent::__construct(
            $context,
            $formKeyValidator,
            $captchaFactory,
            $config,
            $reviewPostDataProcessor,
            $dataObjectHelper,
            $reviewInterfaceFactory,
            $reviewManagement);
    }

    public function execute()
    {
        if ($this->getRequest()->getParam('success_v3')) {
            $postData = $this->getRequest()->getPostValue();
            if (!empty($postData)) {
                try {
                    $preparedData = $this->reviewPostDataProcessor->process($postData);
                    $this->performSubmit($preparedData);
                    $this->messageManager->addSuccessMessage($this->getSuccessMessage());
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addExceptionMessage($e, __('Something went wrong while submitting the review.'));
                }
            }
        } else {
            $this->messageManager->addError(__('We can\'t post your review right now.'));
        }
        return $this->getPreparedRedirect();
    }

    /**
     * @param array $preparedData
     * @return \Aheadworks\AdvancedReviews\Api\Data\ReviewInterface
     * @throws \Magento\Framework\Exception\LocalizedException
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
}