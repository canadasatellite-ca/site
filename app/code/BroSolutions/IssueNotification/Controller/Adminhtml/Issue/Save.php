<?php

namespace BroSolutions\IssueNotification\Controller\Adminhtml\Issue;

/**
 * Class Save
 * @package BroSolutions\IssueNotification\Controller\Adminhtml\Issue
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \BroSolutions\IssueNotification\Model\IssueNotification
     */
    public $issueNotification;

    /**
     * Save constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \BroSolutions\IssueNotification\Model\IssueNotification $issueNotification
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \BroSolutions\IssueNotification\Model\IssueNotification $issueNotification
    ) {
        parent::__construct($context);
        $this->issueNotification = $issueNotification;
    }

    /**
     * @return false|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('issue_id');
        $issue = $this->issueNotification->load($id);

        if (!$issue->getId()) {
            $this->messageManager->addError('Requested item not found');
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath($this->_redirect->getRefererUrl());
        }

        try {
            $issue->setStatus($this->getRequest()->getParam('status'))->save();
            $this->messageManager->addSuccess(__('This item has been updated'));
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath($this->_redirect->getRefererUrl());
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath($this->_redirect->getRefererUrl());
    }
}
