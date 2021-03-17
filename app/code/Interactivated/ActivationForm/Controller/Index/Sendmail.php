<?php

namespace Interactivated\ActivationForm\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\TestFramework\Inspection\Exception;

class Sendmail extends Action
{
    protected $requestFactory;

    protected $resultPageFactory;

    protected $emailSender;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        \Interactivated\ActivationForm\Model\ActivationformFactory $requestFactory,
        \Interactivated\ActivationForm\Email\EmailSender $emailSender
    )
    {
        $this->resultPageFactory = $pageFactory;
        $this->requestFactory = $requestFactory;
        $this->emailSender = $emailSender;
        parent::__construct($context);
    }

    /**
     * The controller action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        if ($params && isset($params['hideit'])) {
            $resultPage = $this->resultPageFactory->create();

            $request = $this->saveData($params);
            $this->prepareAndSendData($request);

            return $resultPage;
        } else {
            return $this->resultRedirectFactory->create()->setPath('activate', ['_current' => true]);
        }
    }

    public function saveData($params) {
        $el = $this->requestFactory->create();
        $el->setData(array(
            'email'=>$params['email'],
            'firstname' => $params['fname'],
            'lastname' => $params['lname'],
            'company' => $params['companyname'],
            'order_number' => $params['orderno'],
            'sim_number' => $params['simno'],
            'notes' => $params['notes'],
            'desired_activation_date' => $params['desiredactivationdate'],
        ))->save();

        return $el;
    }

    public function prepareAndSendData($request) {
        try {
            $this->sendActivationEmails($request);

            return $this->_redirect('*/*/index',array('success'=>'true'));
        } catch (\Exception $e) {
            $this->messageManager->addError( __('Something wrong, try again later.') );
        }
        $this->_redirect('*/*/index');
    }

    private function sendActivationEmails($request) {
        $this->emailSender->sendActivationEmails($request);
    }
}