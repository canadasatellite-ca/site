<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Adminhtml\Queue;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Aheadworks\AdvancedReviews\Api\QueueManagementInterface;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;

/**
 * Class Send
 *
 * @package Aheadworks\AdvancedReviews\Controller\Adminhtml\Queue
 */
class Send extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_AdvancedReviews::mail_log';

    /**
     * @var QueueManagementInterface
     */
    private $queueManagement;

    /**
     * @param Context $context
     * @param QueueManagementInterface $queueManagement
     */
    public function __construct(
        Context $context,
        QueueManagementInterface $queueManagement
    ) {
        parent::__construct($context);
        $this->queueManagement = $queueManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($queueItemId = $this->getRequest()->getParam(QueueItemInterface::ID, false)) {
            try {
                $result = $this->queueManagement->sendById($queueItemId);
                if ($result) {
                    $this->messageManager->addSuccessMessage(__('Email was successfully sent.'));
                } else {
                    $this->messageManager->addErrorMessage(__('This email can not be sent.'));
                }
                return $resultRedirect->setRefererUrl();
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while sending the email.')
                );
            }
        }
        return $resultRedirect->setRefererUrl();
    }
}
