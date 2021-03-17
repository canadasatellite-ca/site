<?php
namespace Magedelight\Faqs\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;

class Save extends Action
{

    /**
     * @var \Magedelight\Faqs\Model\Category
     */
    public $categoryModel;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magedelight\Faqs\Model\Category $categoryModel,
        \Magedelight\Faqs\Model\ResourceModel\Category\CollectionFactory $faqCategoryCollection,
        \Magedelight\Faqs\Model\ResourceModel\Faq\CollectionFactory $faqQuestionsCollection,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\DataObject $DataObject,
        \Magedelight\Faqs\Model\ResourceModel\Question\CollectionFactory $questionModelCollstion,
        \Magento\Framework\Filesystem\Io\File $fileSystem,
        \Magento\Framework\Json\Encoder $jsonEncoder
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->faqCategoryCollection = $faqCategoryCollection;
        $this->faqQuestionsCollection = $faqQuestionsCollection;
        $this->jsonEncoder = $jsonEncoder;
        $this->questionModelCollstion = $questionModelCollstion;
        $this->dataObject = $DataObject;
        $this->fileSystem = $fileSystem;
        $this->categoryModel= $categoryModel;
    }
    
    // @codingStandardsIgnoreStart
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magedelight_Faqs::category');
    }
    // @codingStandardsIgnoreEnd
    
    /**
     * Filter category data
     *
     * @param array $rawData
     * @return array
     */
    protected function _filterCategoryPostData(array $rawData)
    {
        $data = $rawData;
        // @todo It is a workaround to prevent saving this data in category model and it has to be refactored in future
        if (isset($data['image']) && is_array($data['image'])) {
            if (!empty($data['image']['delete'])) {
                $data['image'] = null;
            } else {
                if (isset($data['image'][0]['name']) && isset($data['image'][0]['tmp_name'])) {
                    $data['image_url']  = $data['image'][0]['url'];
                    $data['image'] = $data['image'][0]['name'];
                } else {
                    unset($data['image']);
                }
            }
        }
        return $data;
    }
    
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $data['store_id'] = implode(',', $data['stores']);
        $data['customer_groups'] = implode(',', $data['customer_group_ids']);

        unset($data['stores']);

        unset($data['customer_group_ids']);
        $data = $this->imagePreprocessing($data);
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->categoryModel;
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }
            $model->addData($this->_filterCategoryPostData($data));
            if (isset($data['category_question'])
                && is_string($data['category_question'])
            ) {
                $questions = json_decode($data['category_question'], true);
                if(isset($questions['on'])) {
                    unset($questions['on']);
                }
                $model->setPostedQuestion($questions);
            }
            $this->_eventManager->dispatch(
                'faqs_category_prepare_save',
                ['category' => $model, 'request' => $this->getRequest()]
            );
                $this->createJsonFile();
                $model->save();
                $this->messageManager->addSuccess(__('FAQ Category Saved Successfully.'));
                $data = $this->_getSession()->getFormData(true);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
           
        }
        return $resultRedirect->setPath('*/*/');
    }
    /**
     * Image data preprocessing
     *
     * @param array $data
     *
     * @return array
     */
    public function imagePreprocessing($data)
    {
        if (empty($data['image'])) {
            unset($data['image']);
            $data['image']['delete'] = true;
        }
        return $data;
    }
    public function createJsonFile() {
        
        $stores = $this->storeManager->getStores();
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
    }
}
