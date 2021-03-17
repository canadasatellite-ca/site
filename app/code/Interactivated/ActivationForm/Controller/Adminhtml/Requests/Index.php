<?php

namespace Interactivated\ActivationForm\Controller\Adminhtml\Requests;

use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
{

    public $resultPageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
    // @codingStandardsIgnoreStart
    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Interactivated_ActivationForm::requests');
    }
    // @codingStandardsIgnoreEnd
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Interactivated_ActivationForm::requests');
        $resultPage->addBreadcrumb(__('Activation Requests'), __('Activation Requests'));
        $resultPage->getConfig()->getTitle()->prepend(__('Activation Requests'));
        return $resultPage;
    }
}
