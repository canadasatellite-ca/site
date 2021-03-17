<?php
namespace Magedelight\Faqs\Controller\Adminhtml\Category;
 
use Magento\Backend\App\Action;
 
class Delete extends Action
{
    public $categoryModel;
 
    /**
     * @param Action\Context $context
     * @param \Magedelight\Faqs\Model\Faq $model
     */
    public function __construct(
        Action\Context $context,
        \Magedelight\Faqs\Model\Category $categoryModel
    ) {
        parent::__construct($context);
        $this->categoryModel = $categoryModel;
    }
    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('category_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->categoryModel;
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('item deleted'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addError(__('item does not exist'));
        return $resultRedirect->setPath('*/*/');
    }
}
