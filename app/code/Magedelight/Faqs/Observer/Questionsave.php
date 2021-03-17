<?php

namespace Magedelight\Faqs\Observer;

use Magento\Framework\Event\ObserverInterface;

class Questionsave implements ObserverInterface
{
    public $request;
   
    public $scopeConfig;
    
    public $objectFactory;

    public $faq;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $_scopeConfig,
        \Magento\Framework\ObjectManagerInterface $objectFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magedelight\Faqs\Model\ResourceModel\Faq $faq
    ) {
    
        $this->objectFactory = $objectFactory;
        $this->request = $request;
        $this->scopeConfig = $_scopeConfig;
        $this->faq = $faq;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->scopeConfig->getValue('md_faq/general/enabled_product')) {
            return;
        }
        if (!$this->scopeConfig->getValue('md_faq/general/enabled_faq')) {
            return;
        }
        
        $productId = $observer->getEvent()->getProduct()->getId();  // you will get product object
        $productparams = $this->request->getParams();
        if (isset($productparams['links']) && !empty($productparams['links']['questions'])) {
            $productQuestions = $productparams['links']['questions'];
            $productQuestionsIds = [];
            foreach ($productQuestions as $question) {
                array_push($productQuestionsIds, $question['id']);
            }
            $this->faq->saveFaqRelation($productQuestionsIds, $productId);
        }
        return $this;
    }
}
