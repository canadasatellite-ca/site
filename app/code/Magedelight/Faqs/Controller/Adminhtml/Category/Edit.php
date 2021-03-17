<?php

namespace Magedelight\Faqs\Controller\Adminhtml\Category;

class Edit extends \Magento\Backend\App\Action
{

    public $coreRegistry = null;
    public $resultPageFactory;
    public $categoryModel;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magedelight\Faqs\Model\Category $categoryModel
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
        $this->categoryModel = $categoryModel;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    // @codingStandardsIgnoreStart
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magedelight_Faqs::category');
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
        $resultPage->setActiveMenu('Magedelight_Faqs::faq_categories')
                ->addBreadcrumb(__('FAQ Category'), __('FAQ Category'))
                ->addBreadcrumb(__('Manage FAQ Categories'), __('Manage FAQ Categories'));
        return $resultPage;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->categoryModel;
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This FAQ Category no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }     
        $this->coreRegistry->register('md_faq_category', $model);
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit FAQ Category') : __('New FAQ Category'),
            $id ? __('Edit FAQ Category') : __('New FAQ Category')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('FAQ Categories'));
        $resultPage->getConfig()->getTitle()
                ->prepend($model->getId() ? $model->getTitle() : __('New FAQ Category'));

        return $resultPage;
    }
}
