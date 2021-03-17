<?php

namespace MageSuper\Casat\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection as AppResource;
use Magento\Backend\Helper\Js as JsHelper;
use Magento\Customer\Model\CustomerFactory as CustomerFactory;

class SendMailAfterFaqSubmit implements ObserverInterface
{

    const FAQ_EMAIL_RECIPIENT = 'trans_email/ident_custom2/email';
    const FAQ_NAME_RECIPIENT = 'trans_email/ident_custom2/name';

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
     * @param \Magedelight\Faqs\Model\Faq $model
     */
    public function __construct(
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
    )
    {
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

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $object = $observer->getObject();
        if (get_class($object) == 'Magedelight\Faqs\Model\Faq') {
            if ($object->isObjectNew()) {
                $data = $object->getData();
                $createdby = $data['created_by'];
                $model = $object;
                $this->emailSentAction($data, $createdby, $model);
            }
        }

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
            $emailData['customer_name'] = $model->getCustomerName();
            $emailData['customer_email'] = $model->getCustomerEmail();
            $emailData['target'] = '';
            $emailData['phone'] = $model->getPhone();
            if ($data['tags'] == 'product') {
                $val = '';
                if(isset($_POST['productname'])){
                    $val = $_POST['productname'];
                }
                $emailData['target'] = 'Product: ' . $val;
            } elseif($data['tags'] == 'category'){
                $val = '';
                if(isset($_POST['productname'])){
                    $val = $_POST['productname'];
                }
                $emailData['target'] = 'Category: ' . $val;
            }

            $this->dataObject->setData($emailData);

            $sender = [
                'name' => $this->scopeConfig->getValue(self::FAQ_NAME_RECIPIENT, $storeScope),
                'email' => $this->scopeConfig->getValue(self::FAQ_EMAIL_RECIPIENT, $storeScope),
            ];
            $send_to = $this->scopeConfig->getValue(self::FAQ_EMAIL_RECIPIENT, $storeScope);

            $transport = $this->transportBuilder
                ->setTemplateIdentifier('faq_email_new_question_template')
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['data' => $this->dataObject])
                ->setFrom($sender)
                ->addTo($send_to)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        }
    }
}