<?php
namespace Interactivated\ActivationForm\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    protected $resultPageFactory;

    public function __construct(Context $context, PageFactory $pageFactory)
    {
        $this->resultPageFactory = $pageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $isSuccess = $this->getRequest()->getParam('success');
        if ($isSuccess=='true'){
            $this->messageManager->addSuccess( __('Your activation request has been received and is in progress. The activation may take up to 2 business days to complete. Once your service(s) are activated, you will receive an Activation Confirmation email, which will include your new satellite phone number and expiry date (if applicable).') );
        }
        $resultPage = $this->resultPageFactory->create();

        return $resultPage;
    }
}