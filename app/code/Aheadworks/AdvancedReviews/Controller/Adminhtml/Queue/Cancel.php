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
 * Class Cancel
 *
 * @package Aheadworks\AdvancedReviews\Controller\Adminhtml\Queue
 */
class Cancel extends Action
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
                $this->queueManagement->cancelById($queueItemId);
                $this->messageManager->addSuccessMessage(__('Email was successfully cancelled.'));
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while cancelling the email.')
                );
            }
        }
        return $resultRedirect->setRefererUrl();
    }
}
