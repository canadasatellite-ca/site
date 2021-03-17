<?php

namespace Interactivated\ActivationForm\Controller\Adminhtml\Requests;

class Edit extends \Magento\Backend\App\Action
{

    public $coreRegistry = null;
    public $resultPageFactory;
    public $activationformModel;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Interactivated\ActivationForm\Model\Activationform $activationformModel
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
        $this->activationformModel = $activationformModel;
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
        $id = $this->getRequest()->getParam('id');
        $model = $this->activationformModel;
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Form no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }
        $this->coreRegistry->register('current_interactivated_activation_form', $model);
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            __('Edit Data'),
            __('Edit Data')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Activation Form'));
        $resultPage->getConfig()->getTitle()
                ->prepend($model->getId() ? $model->getTitle() : __('New Activation Form'));

        return $resultPage;
    }
}
