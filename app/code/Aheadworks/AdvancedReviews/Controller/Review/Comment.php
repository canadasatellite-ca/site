<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Review;

use Aheadworks\AdvancedReviews\Api\Data\CommentInterface;
use Aheadworks\AdvancedReviews\Api\Data\CommentInterfaceFactory;
use Aheadworks\AdvancedReviews\Model\Captcha\CaptchaAdapterInterface;
use Aheadworks\AdvancedReviews\Model\Config;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Aheadworks\AdvancedReviews\Model\Captcha\Factory as CaptchaFactory;
use Aheadworks\AdvancedReviews\Api\CommentManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Comment
 * @package Aheadworks\AdvancedReviews\Controller\Review
 */
class Comment extends AbstractPostAction
{
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var CommentManagementInterface
     */
    private $commentManagement;

    /**
     * @var CommentInterfaceFactory
     */
    private $commentFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Context $context
     * @param FormKeyValidator $formKeyValidator
     * @param CaptchaFactory $captchaFactory
     * @param Config $config
     * @param JsonFactory $resultJsonFactory
     * @param CommentManagementInterface $commentManagement
     * @param CommentInterfaceFactory $commentFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        FormKeyValidator $formKeyValidator,
        CaptchaFactory $captchaFactory,
        Config $config,
        JsonFactory $resultJsonFactory,
        CommentManagementInterface $commentManagement,
        CommentInterfaceFactory $commentFactory,
        DataObjectHelper $dataObjectHelper,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context, $formKeyValidator, $captchaFactory, $config);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->commentManagement = $commentManagement;
        $this->commentFactory = $commentFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $success = false;

        if (!empty($params)) {
            try {
                $this->validate($this->resolveFormId($params));
                $this->performAddComment($params);
                $success = true;
            } catch (LocalizedException $e) {
                $data = [
                    'success' => $success,
                    'message' => $e->getMessage(),
                    'refresh' => false
                ];
                return $this->resultJsonFactory->create()->setData($data);
            }
            $data = [
                'success' => $success,
                'message' => $this->getSuccessMessage(),
                'refresh' => $this->config->isAutoApproveCommentsEnabled()
            ];
        } else {
            $data = [
                'success' => $success,
                'message' => __('Something went wrong. Please try again later.'),
                'refresh' => false
            ];
        }

        return $this->resultJsonFactory->create()->setData($data);
    }

    /**
     * Add new comment
     *
     * @param array $data
     * @throws LocalizedException
     */
    private function performAddComment($data)
    {
        /** @var CommentInterface $comment */
        $comment = $this->commentFactory->create();
        $storeId = $this->storeManager->getStore()->getId();

        $this->dataObjectHelper->populateWithArray(
            $comment,
            $data,
            CommentInterface::class
        );
        $this->commentManagement->addCustomerComment($comment, $storeId);
    }

    /**
     * Resolve form id
     *
     * @param array $params
     * @return string
     */
    private function resolveFormId($params)
    {
        return isset($params['row_index'])
            ? CaptchaAdapterInterface::COMMENT_FORM_ID_BASE . $params['row_index']
            : '';
    }

    /**
     * Retrieve success message
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage()
    {
        return $this->config->isAutoApproveCommentsEnabled()
            ? __('Thank you for your comment.')
            : __('You submitted your comment for moderation.');
    }
}
