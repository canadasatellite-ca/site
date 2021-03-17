<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Abuse;

use Aheadworks\AdvancedReviews\Model\Source\AbuseReport\Type;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;
use Aheadworks\AdvancedReviews\Api\AbuseReportManagementInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\Http as HttpRequest;

/**
 * Class Report
 * @package Aheadworks\AdvancedReviews\Controller\Abuse
 */
class Report extends Action
{
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var AbuseReportManagementInterface
     */
    private $abuseReportManagement;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var FormKeyValidator
     */
    private $formKeyValidator;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param AbuseReportManagementInterface $abuseReportManagement
     * @param StoreManagerInterface $storeManager
     * @param FormKeyValidator $formKeyValidator
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        AbuseReportManagementInterface $abuseReportManagement,
        StoreManagerInterface $storeManager,
        FormKeyValidator $formKeyValidator
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->abuseReportManagement = $abuseReportManagement;
        $this->storeManager = $storeManager;
        $this->formKeyValidator = $formKeyValidator;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $data = [
            'success' => false,
            'message' => __('Something went wrong.')
        ];
        /** @var RequestInterface|HttpRequest $request */
        $request = $this->getRequest();
        $isFromKeyValid = $this->formKeyValidator->validate($request);
        if ($isFromKeyValid && $request->isPost()) {
            $params = $request->getPostValue();
            $entityId = isset($params['entityId']) ? $params['entityId'] : null;
            $entityType = isset($params['entityType']) ? $params['entityType'] : null;

            if ($entityId && $entityType) {
                try {
                    $storeId = $this->storeManager->getStore()->getId();
                    if ($entityType == Type::REVIEW) {
                        $this->abuseReportManagement->reportReview($entityId, $storeId);
                    }
                    if ($entityType == Type::COMMENT) {
                        $this->abuseReportManagement->reportComment($entityId, $storeId);
                    }
                    $data = [
                        'success' => true,
                        'message' => __('Thank you for your report. We will check it as soon as possible.')
                    ];
                } catch (\Exception $e) {
                }
            }
        }

        return $this->resultJsonFactory->create()->setData($data);
    }
}
