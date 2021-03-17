<?php
namespace Interactivated\ActivationForm\Email;

class EmailSender
{
	const SENDER_NAME = 'CANADA SATELLITE';
    const SENDER_EMAIL = 'sales@canadasatellite.ca';

    const XML_PATH_EMAIL_RECIPIENT = 'trans_email/ident_general/email';
    const XML_PATH_NAME_RECIPIENT = 'trans_email/ident_general/name';


    protected $objectManager;

    protected $inlineTranslation;
    protected $dataObject;
    protected $escaper;
    protected $scopeConfig;
    protected $transportBuilder;

    public function __construct(
    	\Magento\Framework\ObjectManagerInterface $objectManager,
    	\Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\DataObject $DataObject,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    ) {
    	$this->objectManager = $objectManager;

    	$this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->dataObject = $DataObject;
        $this->escaper = $escaper;
        $this->inlineTranslation = $inlineTranslation;
    }

    public function sendActivationEmails($request) {
        $receiverInfo = [
            'name' => $request->getFirstname() . ' ' . $request->getLastname(),
            'email' => $request->getEmail()
        ];

        $senderInfo = [
            'name' => self::SENDER_NAME,
            'email' => self::SENDER_EMAIL,
        ];

        $this->dataObject->setData($request->getData());

        $this->objectManager->get('Interactivated\ActivationForm\Helper\Email')->sendActivationEmailToCustomer(
            ['data' => $this->dataObject],
            $senderInfo,
            $receiverInfo
        );

        $this->objectManager->get('Interactivated\ActivationForm\Helper\Email')->sendActivationEmailToSales(
            ['data' => $this->dataObject],
            $senderInfo,
            $senderInfo
        );
    }
    
    public function sendActivationConfirmationEmail($request)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $emailData = [];
        $this->inlineTranslation->suspend();
        $emailData = $request;
        $this->dataObject->setData($emailData);

        $customer_email = $request['email'];

        $sender = [
            'name' => $this->escaper->escapeHtml(
                $this->scopeConfig->getValue(self::XML_PATH_NAME_RECIPIENT, $storeScope)
            ),
            'email' => $this->escaper->escapeHtml(
                $this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope)
            ),
        ];

        $transport = $this->transportBuilder
            ->setTemplateIdentifier('interactivated_activationform_confirm_email_template')
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
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