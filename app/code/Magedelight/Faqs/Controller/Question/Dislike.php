<?php
 
namespace Magedelight\Faqs\Controller\Question;
 
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Dislike extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    public $faqFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magedelight\Faqs\Model\FaqFactory  $faqFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->faqFactory = $faqFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    

    /**
     * Product view action
     *
     * @return \Magento\Framework\Controller\Result\Forward|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {   
        $question_id = $this->getRequest()->getParam('question_id');
        if ($question_id) {
            $model = $this->faqFactory->create();
            $model->load($question_id);
            $currentDislikes = $model->getDislike();
            $updateDislikes = $currentDislikes + 1 ; 
            $model->setData('like', $updateDislikes);
            $model->save();
            echo $updateDislikes;
        }
        
    }
}