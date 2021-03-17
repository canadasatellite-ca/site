<?php
namespace Magedelight\Faqs\Controller\Adminhtml\Faq;

use Magento\Backend\App\Action;
use Magento\Backend\Helper\Js as JsHelper;
use Magento\Customer\Model\CustomerFactory as CustomerFactory;
 
class Save extends Action
{
    /**
    * Recipient email config path
    */
    const XML_PATH_EMAIL_RECIPIENT = 'trans_email/ident_general/email';
    const XML_PATH_NAME_RECIPIENT = 'trans_email/ident_general/name';
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    public $transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    public $inlineTranslation;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;
    /**
     * @var \Magento\Framework\Escaper
     */
    public $escaper;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    /**
     * @var \Magedelight\Faqs\Model\Faq
     */
    public $dataObject;
    public $model;
    /**
     * @var \Magento\Backend\Helper\Js
     */
    public $jsHelper;
    /**
     * @var\Magento\Customer\Model\Customer
     */
    public $customerFactory;
    /**
     * @param Action\Context $context
     * @param \Magedelight\Faqs\Model\Faq $model
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magedelight\Faqs\Model\ResourceModel\Category\CollectionFactory $faqCategoryCollection,
        \Magedelight\Faqs\Model\ResourceModel\Faq\CollectionFactory $faqQuestionsCollection,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magedelight\Faqs\Model\ResourceModel\Question\CollectionFactory $questionModelCollstion,
        \Magento\Framework\Filesystem\Io\File $fileSystem,
        \Magento\Framework\Json\Encoder $jsonEncoder,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\DataObject $DataObject,
        \Magedelight\Faqs\Model\Faq $model,
        JsHelper $jsHelper,
        CustomerFactory $customerFactory
    ) {
        parent::__construct($context);
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->escaper = $escaper;
        $this->model = $model;
        $this->jsHelper = $jsHelper;
        $this->dataObject = $DataObject;
        $this->customerFactory = $customerFactory;
        $this->faqCategoryCollection = $faqCategoryCollection;
        $this->faqQuestionsCollection = $faqQuestionsCollection;
        $this->jsonEncoder = $jsonEncoder;
        $this->questionModelCollstion = $questionModelCollstion;
        $this->fileSystem = $fileSystem;
    }
 
    /**
     * {@inheritdoc}
     */
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magedelight_Faqs::faq');
    }
    
    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $data['store_id'] = implode(',', $data['stores']);
        unset($data['stores']);
        
        if ($data['question_type'] == \Magedelight\Faqs\Model\Faq::PRODUCT_FAQ) {
            $data['category_id'] = null;
        }
        $data['created_by'] = \Magedelight\Faqs\Model\Faq::ADMIN_CUSTOMER;
        $resultRedirect = $this->resultRedirectFactory->create();
        $createdby = '';
        if ($data) {
            $model = $this->model;
            
            $id = $this->getRequest()->getParam('question_id'); 
            if ($id) {
                $model->load($id);
                $createdby = $model->getCreatedBy();
                $data['created_by'] = $createdby;
            }  else {
                $data['created_by'] = \Magedelight\Faqs\Model\Faq::ADMIN_CUSTOMER;    
            }
            
            /*if (isset($id) && !empty($id)) {
                $model->load($id);
                $createdby = $model->getCreatedBy();
                $customerId = $model->getCustomerId();
                $data['created_by'] = $createdby;
                $data['guest_name'] = $model->getGuestName();
                $data['guest_email'] = $model->getGuestEmail();
            } */
            $this->createJsonFile();
            $model->setData($data);
            if (isset($data['category_products'])
                && is_string($data['category_products'])
            ) {
                $products = json_decode($data['category_products'], true);
                if(isset($products['on'])) {
                    unset($products['on']);
                }
                $model->setPostedProducts($products);
            }
            
            $this->_eventManager->dispatch(
                'faqs_faq_prepare_save',
                ['faq' => $model, 'request' => $this->getRequest()]
            );
            
            if (isset($data['email_send']) && $data['email_send'] == true) {
                $this->emailSentAction($data, $createdby, $model);
            }
            
            $model->save();
            $this->messageManager->addSuccess(__('FAQ question Saved Successfully'));
            return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
            
        }
        return $resultRedirect->setPath('*/*/');
    }
    
    public function emailSentAction($data, $createdby, $model)
    {   
        if ($createdby == \Magedelight\Faqs\Model\Faq::LOGIN_CUSTOMER ||
                $createdby == \Magedelight\Faqs\Model\Faq::GUEST_CUSTOMER
        ) {
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $emailData = [];
            $this->inlineTranslation->suspend();
            $emailData['question'] = $data['question'];
            $emailData['answer'] = $data['answer'];
            $this->dataObject->setData($emailData);
           
            $customer_name = $model->getCustomerName();
            $customer_email = $model->getCustomerEmail();
            
            $sender = [
                'name' => $this->escaper->escapeHtml(
                    $this->scopeConfig->getValue(self::XML_PATH_NAME_RECIPIENT, $storeScope)
                ),
                'email' => $this->escaper->escapeHtml(
                    $this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope)
                ),
            ];
            
            $transport = $this->transportBuilder
                    ->setTemplateIdentifier('faq_email_email_template')
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_ADMINHTML,
                            'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                        ]
                    )
                    ->setTemplateVars(['data' => $this->dataObject])
                    ->setFrom($sender)
                    ->addTo($customer_email)
                    ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        }
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
