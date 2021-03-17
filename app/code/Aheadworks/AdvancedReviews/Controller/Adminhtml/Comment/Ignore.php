<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Adminhtml\Comment;

use Aheadworks\AdvancedReviews\Api\Data\CommentInterface;
use Aheadworks\AdvancedReviews\Api\AbuseReportManagementInterface;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;

/**
 * Class Ignore
 * @package Aheadworks\AdvancedReviews\Controller\Adminhtml\Comment
 */
class Ignore extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_AdvancedReviews::comments';

    /**
     * @var AbuseReportManagementInterface
     */
    private $abuseReportManagement;

    /**
     * @param Context $context
     * @param AbuseReportManagementInterface $abuseReportManagement
     */
    public function __construct(
        Context $context,
        AbuseReportManagementInterface $abuseReportManagement
    ) {
        parent::__construct($context);
        $this->abuseReportManagement = $abuseReportManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($commentId = $this->getRequest()->getParam(CommentInterface::ID, false)) {
            try {
                $this->abuseReportManagement->ignoreAbuseForComments([$commentId]);
                $this->messageManager->addSuccessMessage(__('Abuse report for comment was ignored.'));
                return $resultRedirect->setRefererUrl();
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while editing the comment.')
                );
            }
        }
        return $resultRedirect->setRefererUrl();
    }
}
