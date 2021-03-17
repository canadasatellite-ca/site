<?php

namespace Interactivated\ActivationForm\Controller\Adminhtml\Requests;

class Save extends \Magento\Backend\App\Action
{
    public $coreRegistry = null;
    public $resultPageFactory;
    public $activationformModel;
    public $emailSender;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Interactivated\ActivationForm\Model\Activationform $activationformModel,
        \Interactivated\ActivationForm\Email\EmailSender $emailSender
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
        $this->activationformModel = $activationformModel;
        $this->emailSender = $emailSender;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    // @codingStandardsIgnoreStart
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Interactivated_ActivationForm::requests');
    }
   // @codingStandardsIgnoreEnd
    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    private function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Interactivated_ActivationForm::requests')
                ->addBreadcrumb(__('Activation Form'), __('Activation Form'))
                ->addBreadcrumb(__('Edit'), __('Edit'));
        return $resultPage;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->activationformModel;
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }
            $data['completed_date'] = date('Y-m-d H:i:s');
            $model->addData($data);
            $this->_eventManager->dispatch(
                'interactivated_activationForm_prepare_save',
                ['category' => $model, 'request' => $this->getRequest()]
            );
            $this->emailSentAction($data, $model);
            $model->setData('status',2);
            $model->save();
            $this->messageManager->addSuccess(__('ActivationForm confirmed.'));
            $data = $this->_getSession()->getFormData(true);
            return $resultRedirect->setPath('*/*/');
        }
        return $resultRedirect->setPath('*/*/');
    }

    public function emailSentAction($data, $model)
    {
        $this->sendActivationConfirmationEmail($data, $model);
    }

    public function sendActivationConfirmationEmail($data, $model)
    {
        $this->emailSender->sendActivationConfirmationEmail($data);
    }
}
