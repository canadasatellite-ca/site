<?php

namespace Magedelight\Faqs\Controller\Adminhtml\Category;

class IndexCategories extends \Magento\Backend\App\Action
{

    public $storeManager;
    public $fileSystem;
    public $dataObject;
    public $faqCategoryCollection;
    public $faqQuestionsCollection;
    public $jsonEncoder;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magedelight\Faqs\Model\ResourceModel\Category\CollectionFactory $faqCategoryCollection,
        \Magedelight\Faqs\Model\ResourceModel\Faq\CollectionFactory $faqQuestionsCollection,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\DataObject $DataObject,
        \Magedelight\Faqs\Model\ResourceModel\Question\CollectionFactory $questionModelCollstion,
        \Magento\Framework\Filesystem\Io\File $fileSystem,
        \Magento\Framework\Json\Encoder $jsonEncoder
    ) {
        $this->storeManager = $storeManager;
        $this->faqCategoryCollection = $faqCategoryCollection;
        $this->faqQuestionsCollection = $faqQuestionsCollection;
        $this->jsonEncoder = $jsonEncoder;
        $this->questionModelCollstion = $questionModelCollstion;
        $this->dataObject = $DataObject;
        $this->fileSystem = $fileSystem;
        parent::__construct($context);
    }

    public function execute()
    {
        $stores = $this->storeManager->getStores();
        $resultRedirect = $this->resultRedirectFactory->create();

        foreach ($stores as $store) {
            $folder = $store->getBaseMediaDir() . DIRECTORY_SEPARATOR . 'md' . DIRECTORY_SEPARATOR . 'faq'
                    . DIRECTORY_SEPARATOR;
            if (!$this->fileSystem->fileExists($folder)) {
                $this->fileSystem->mkdir($folder, 0777, true);
            }
            $datafile = $folder . 'store_' . $store->getId() . '.txt';
            $arrayData = [];
            $categoryCollection = $this->faqCategoryCollection->create()
                    ->addFieldToFilter(
                        ['store_id', 'store_id'],
                        [
                            ["finset" => [
                            $store->getId()]
                            ],
                        ["finset" => [0]]
                        ]
                    )
                    ->addFieldToFilter('status', ['eq' => \Magedelight\Faqs\Model\Category::STATUS_ENABLED])
                    ->setOrder('position', 'ASC');
            if ($categoryCollection) {
                foreach ($categoryCollection as $_faqCategory) {
                    $questionIds = $this->questionModelCollstion->create()
                            ->addFieldToFilter('category_id', ['eq' => $_faqCategory->getCategoryId()])
                            ->getData();
                    $questionCollection = $this->faqQuestionsCollection->create()
                            ->addFieldToFilter('question_id', [
                               'in' => $questionIds
                            ])
                            ->addFieldToFilter('status', ['eq' => \Magedelight\Faqs\Model\Faq::STATUS_ENABLED])
                            ->addFieldToFilter('answer', ['notnull' => true])
                            ->setOrder('position', 'ASC');
                    
                    if ($questionCollection) {
                        foreach ($questionCollection as $_faqQuestion) {
                            $arrayData[] = [
                                'label' => $_faqQuestion->getQuestion(),
                                'category' => $_faqCategory->getTitle(),
                                'category_id' => $_faqCategory->getCategoryId(),
                                'question_id' => $_faqQuestion->getId()
                            ];
                        }
                    }
                }
            }
            $objectData = $this->dataObject->addData($arrayData);
            $jsonString = $this->jsonEncoder->encode($objectData);
            $this->fileSystem->write($datafile, $jsonString);
        }
        $this->messageManager->addSuccess(__('FAQ Categories Indexed Successfully'));
        return $resultRedirect->setPath('*/*/');
    }
}
