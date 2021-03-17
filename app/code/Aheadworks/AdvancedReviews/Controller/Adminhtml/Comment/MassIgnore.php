<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Adminhtml\Comment;

use Magento\Backend\App\Action\Context;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Comment\CollectionFactory;
use Aheadworks\AdvancedReviews\Api\AbuseReportManagementInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class MassIgnore
 * @package Aheadworks\AdvancedReviews\Controller\Adminhtml\Comment
 */
class MassIgnore extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_AdvancedReviews::reviews';

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var AbuseReportManagementInterface
     */
    protected $abuseReportManagement;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param AbuseReportManagementInterface $abuseReportManagement
     * @param Filter $filter
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        AbuseReportManagementInterface $abuseReportManagement,
        Filter $filter
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->abuseReportManagement = $abuseReportManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $commentsIds = $this->getCommentIdsForMassAction();
            if ($commentsIds) {
                $this->abuseReportManagement->ignoreAbuseForComments($commentsIds);
                $this->messageManager->addSuccessMessage(
                    __('Abuse reports marked as moderated for selected comments.')
                );
            } else {
                $this->messageManager->addSuccessMessage(__('No records were changed.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setRefererUrl();
        return $resultRedirect;
    }

    /**
     * Retrieve array of comment ids for mass action
     *
     * @return array
     */
    protected function getCommentIdsForMassAction()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $ids = $collection->getAllIds();
        } catch (LocalizedException $exception) {
            $ids = [];
        }

        return $ids;
    }
}
