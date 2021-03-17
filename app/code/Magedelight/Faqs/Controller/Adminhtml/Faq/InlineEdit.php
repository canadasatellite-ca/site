<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magedelight\Faqs\Controller\Adminhtml\Faq;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magedelight\Faqs\Model\FaqFactory;
use Magedelight\Faqs\Controller\Adminhtml\Faq as FaqController;
use Magento\Framework\Registry;

class InlineEdit extends FaqController
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
        FaqFactory $faqFactory,
        Context $context
    ) {
        $this->jsonFactory = $jsonFactory;
        parent::__construct($registry, $faqFactory, $context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
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
        
        foreach (array_keys($postItems) as $faqId) {
            $faq = $this->dataLoad($faqId);
            try {
                $faqData = $this->filterData($postItems[$faqId]);
                $faq->addData($faqData);
                $this->dataSave($faq);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithFaqtId($faq, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithFaqtId($faq, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithFaqtId(
                    $faq,
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
    * Add faq id to error message
    *
    * @param Faq $faq
    * @param string $errorText
    * @return string
    */
    private function getErrorWithFaqtId($faq, $errorText)
    {
        return '[Faq ID: ' . $faq->getId() . '] ' . $errorText;
    }
    
    private function dataSave($faq)
    {
        $faq->save();
    }
    
    private function dataLoad($faqId)
    {
        return $this->faqFactory->create()->load($faqId);
    }
}
