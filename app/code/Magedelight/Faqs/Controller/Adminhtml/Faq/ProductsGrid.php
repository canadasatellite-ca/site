<?php

namespace Magedelight\Faqs\Controller\Adminhtml\Faq;

class ProductsGrid extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * Grid Action
     * Display list of products related to current category
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {   
        $id = (int)$this->getRequest()->getParam('id', false);
        $myModel = $this->_objectManager->create('Magedelight\Faqs\Model\Faq');
        if ($id) {
            $myModel->load($id);            
        }
        $this->_objectManager->get('Magento\Framework\Registry')->register('md_faqs_question', $myModel);
        $this->_objectManager->get('Magento\Cms\Model\Wysiwyg\Config');
        
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                'Magedelight\Faqs\Block\Adminhtml\Faq\Edit\Tab\Product',
                'category.product.grid'
            )->toHtml()
        );
    }
}
?>