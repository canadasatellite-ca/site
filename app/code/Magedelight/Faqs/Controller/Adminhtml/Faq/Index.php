<?php
namespace Magedelight\Faqs\Controller\Adminhtml\Faq;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;
 
class Index extends Action
{
    const ADMIN_RESOURCE = 'Magedelight_Faqs::faq';
 
    /**
     * @var PageFactory
     */
    public $resultPageFactory;
 
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
 
    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magedelight_Faqs::faq');
        $resultPage->addBreadcrumb(__('Faqs'), __('Faqs'));
        $resultPage->addBreadcrumb(__('Manage Faq'), __('Manage Faq'));
        $resultPage->getConfig()->getTitle()->prepend(__('Questions'));
        return $resultPage;
    }
}
