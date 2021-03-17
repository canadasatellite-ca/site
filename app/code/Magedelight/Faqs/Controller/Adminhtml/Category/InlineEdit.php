<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magedelight\Faqs\Controller\Adminhtml\Category;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magedelight\Faqs\Model\CategoryFactory;
use Magedelight\Faqs\Controller\Adminhtml\Category as CategoryController;
use Magento\Framework\Registry;

class InlineEdit extends CategoryController
{
    /**
     * @var JsonFactory
     */
    public $jsonFactory;

    /**
     * @param JsonFactory $jsonFactory
     * @param Registry $registry
     * @param Context $context
     */
    public function __construct(
        JsonFactory $jsonFactory,
        Registry $registry,
        CategoryFactory $categoryfactory,
        Context $context
    ) {
        $this->jsonFactory = $jsonFactory;
        parent::__construct($registry, $categoryfactory, $context);
    }
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && !empty($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }
        
        foreach (array_keys($postItems) as $categoryId) {
            $category = $this->dataLoad($categoryId);
            try {
                $categoryData = $this->filterData($postItems[$categoryId]);
                $category->addData($categoryData);
                $this->dataSave($category);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithCategorytId($category, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithCategorytId($category, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithCategorytId(
                    $category,
                    __('Something went wrong while saving the page.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
    /**
     * Add category id to error message
     *
     * @param Category $category
     * @param string $errorText
     * @return string
     */
    private function getErrorWithCategorytId($category, $errorText)
    {
        return '[Category ID: ' . $category->getId() . '] ' . $errorText;
    }
    
    private function dataSave($category)
    {
        $category->save();
    }
    
    private function dataLoad($categoryId)
    {
        return $this->categoryFactory->create()->load($categoryId);
    }
}
